<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\TraineeAssessment;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    public function show(Subscription $subscription)
    {
        $this->authorizeSubscription($subscription);

        $assessment = TraineeAssessment::where('subscription_id', $subscription->id)
            ->where('user_id', Auth::id())
            ->first();

        // Already submitted — go to dashboard
        if ($assessment?->submitted_at) {
            return redirect()->route('dashboard')
                ->with('info', 'لقد أرسلت استمارة التقييم بالفعل.');
        }

        return view('app.web.assessment', compact('subscription', 'assessment'));
    }

    public function store(Request $request, Subscription $subscription)
    {
        $this->authorizeSubscription($subscription);

        // Prevent re-submission
        $existing = TraineeAssessment::where('subscription_id', $subscription->id)
            ->where('user_id', Auth::id())
            ->whereNotNull('submitted_at')
            ->first();

        if ($existing) {
            return redirect()->route('dashboard')
                ->with('info', 'لقد أرسلت استمارة التقييم بالفعل.');
        }

        $validated = $request->validate([
            // Section 1
            'date_of_birth'  => ['required', 'date', 'before:-10 years'],
            'current_weight' => ['required', 'numeric', 'min:30', 'max:300'],
            'target_weight'  => ['required', 'numeric', 'min:30', 'max:300'],
            'height'         => ['required', 'numeric', 'min:100', 'max:250'],

            // Section 2
            'primary_goal'             => ['required', 'in:weight_loss,muscle_gain,endurance,flexibility,general_fitness,other'],
            'experience_level'         => ['required', 'in:beginner,intermediate,advanced'],
            'workout_days_per_week'    => ['required', 'integer', 'min:1', 'max:7'],
            'session_duration_minutes' => ['required', 'integer', 'in:30,45,60,90'],

            // training_details (json bucket)
            'training_location'        => ['required', 'in:home,gym,outdoor,both'],
            'equipment_level'          => ['required', 'in:none,basic,full'],
            'target_duration_weeks'    => ['required', 'in:12,16,24'],
            'fitness_score'            => ['required', 'integer', 'min:1', 'max:10'],

            // Section 3 — nutrition (json bucket)
            'meals_count'     => ['required', 'integer', 'min:1', 'max:10'],
            'has_snacks'      => ['required', 'in:yes,no'],
            'diet_type'       => ['required', 'in:normal,vegetarian,vegan,keto,low_carb,other'],
            'water_intake'    => ['required', 'numeric', 'min:0.5', 'max:10'],
            'has_supplements' => ['required', 'in:yes,no'],
            'supplements_details' => ['nullable', 'string', 'max:300'],
            'preferred_foods' => ['nullable', 'string', 'max:500'],
            'disliked_foods'  => ['nullable', 'string', 'max:500'],

            // Section 4 — health (json bucket)
            'has_injuries'      => ['required', 'in:yes,no'],
            'injuries_details'  => ['nullable', 'string', 'max:500'],
            'health_conditions' => ['nullable', 'string', 'max:500'],
            'allergies'         => ['nullable', 'string', 'max:300'],
            'medications'       => ['nullable', 'string', 'max:300'],

            // Section 5 — lifestyle (json bucket)
            'daily_activity'   => ['required', 'in:sedentary,light,moderate,active,very_active'],
            'sleep_hours'      => ['required', 'numeric', 'min:3', 'max:14'],
            'smoking'          => ['required', 'in:yes,no'],
            'commitment_score' => ['required', 'integer', 'min:1', 'max:10'],
            'challenges'       => ['nullable', 'string', 'max:500'],

            // Section 6 — declaration
            'declaration_accepted' => ['required', 'accepted'],
            'signature_text'       => ['required', 'string', 'min:2', 'max:100'],
        ], [
            'date_of_birth.required'          => 'تاريخ الميلاد مطلوب',
            'date_of_birth.before'            => 'يجب أن تكون عمرك 10 سنوات على الأقل',
            'current_weight.required'         => 'الوزن الحالي مطلوب',
            'target_weight.required'          => 'الوزن المستهدف مطلوب',
            'height.required'                 => 'الطول مطلوب',
            'primary_goal.required'           => 'الهدف الرئيسي مطلوب',
            'experience_level.required'       => 'مستوى اللياقة مطلوب',
            'workout_days_per_week.required'  => 'عدد أيام التمرين مطلوب',
            'session_duration_minutes.required' => 'مدة الجلسة مطلوبة',
            'training_location.required'      => 'مكان التدريب مطلوب',
            'equipment_level.required'        => 'مستوى المعدات مطلوب',
            'target_duration_weeks.required'  => 'المدة المستهدفة مطلوبة',
            'fitness_score.required'          => 'تقييم اللياقة مطلوب',
            'meals_count.required'            => 'عدد الوجبات مطلوب',
            'has_snacks.required'             => 'حقل الوجبات الخفيفة مطلوب',
            'diet_type.required'              => 'نوع النظام الغذائي مطلوب',
            'water_intake.required'           => 'كمية الماء مطلوبة',
            'has_supplements.required'        => 'حقل المكملات مطلوب',
            'has_injuries.required'           => 'حقل الإصابات مطلوب',
            'daily_activity.required'         => 'مستوى النشاط اليومي مطلوب',
            'sleep_hours.required'            => 'عدد ساعات النوم مطلوب',
            'smoking.required'                => 'حقل التدخين مطلوب',
            'commitment_score.required'       => 'تقييم الالتزام مطلوب',
            'declaration_accepted.required'   => 'يجب الموافقة على الإقرار',
            'declaration_accepted.accepted'   => 'يجب الموافقة على الإقرار للمتابعة',
            'signature_text.required'         => 'التوقيع مطلوب',
        ]);

        DB::transaction(function () use ($validated, $subscription) {
            $userId = Auth::id();

            TraineeAssessment::updateOrCreate(
                ['subscription_id' => $subscription->id, 'user_id' => $userId],
                [
                    // Section 1
                    'date_of_birth'  => $validated['date_of_birth'],
                    'current_weight' => $validated['current_weight'],
                    'target_weight'  => $validated['target_weight'],
                    'height'         => $validated['height'],

                    // Section 2
                    'primary_goal'             => $validated['primary_goal'],
                    'experience_level'         => $validated['experience_level'],
                    'workout_days_per_week'    => $validated['workout_days_per_week'],
                    'session_duration_minutes' => $validated['session_duration_minutes'],

                    'training_details' => [
                        'location'             => $validated['training_location'],
                        'equipment_level'      => $validated['equipment_level'],
                        'target_duration_weeks'=> $validated['target_duration_weeks'],
                        'fitness_score'        => $validated['fitness_score'],
                    ],

                    // Section 3
                    'nutrition' => [
                        'meals_count'        => $validated['meals_count'],
                        'has_snacks'         => $validated['has_snacks'],
                        'diet_type'          => $validated['diet_type'],
                        'water_intake'       => $validated['water_intake'],
                        'has_supplements'    => $validated['has_supplements'],
                        'supplements_details'=> $validated['supplements_details'] ?? null,
                        'preferred_foods'    => $validated['preferred_foods'] ?? null,
                        'disliked_foods'     => $validated['disliked_foods'] ?? null,
                    ],

                    // Section 4
                    'health' => [
                        'has_injuries'      => $validated['has_injuries'],
                        'injuries_details'  => $validated['injuries_details'] ?? null,
                        'health_conditions' => $validated['health_conditions'] ?? null,
                        'allergies'         => $validated['allergies'] ?? null,
                        'medications'       => $validated['medications'] ?? null,
                    ],

                    // Section 5
                    'lifestyle' => [
                        'daily_activity'   => $validated['daily_activity'],
                        'sleep_hours'      => $validated['sleep_hours'],
                        'smoking'          => $validated['smoking'],
                        'commitment_score' => $validated['commitment_score'],
                        'challenges'       => $validated['challenges'] ?? null,
                    ],

                    // Section 6
                    'declaration_accepted_at' => now(),
                    'signature_text'          => $validated['signature_text'],

                    'submitted_at' => now(),
                ]
            );

            // Sync key metrics to user_profiles (coach reads from here in confirmBooking)
            // start_weight is set only on INSERT (first time) — not overwritten on UPDATE
            $profileExists = UserProfile::where('user_id', $userId)->exists();
            UserProfile::updateOrCreate(
                ['user_id' => $userId],
                array_merge(
                    [
                        'date_of_birth'  => $validated['date_of_birth'],
                        'height'         => $validated['height'],
                        'current_weight' => $validated['current_weight'],
                        'goal_weight'    => $validated['target_weight'],
                    ],
                    $profileExists ? [] : ['start_weight' => $validated['current_weight']]
                )
            );
        });

        return redirect()->route('booking.show', $subscription)
            ->with('success', 'تم إرسال استمارة التقييم بنجاح! يمكنك الآن حجز موعد جلستك الأولى.');
    }

    private function authorizeSubscription(Subscription $subscription): void
    {
        if ((int) $subscription->user_id !== (int) Auth::id()) {
            abort(403);
        }

        if ($subscription->status !== 'approved') {
            abort(403, 'هذا الاشتراك غير متاح لإكمال الاستمارة.');
        }
    }
}
