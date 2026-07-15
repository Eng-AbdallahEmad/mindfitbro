<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payment Methods (Manual Bank Transfer / InstaPay)
    |--------------------------------------------------------------------------
    | Each key maps to a set of payment instructions shown at checkout.
    | currency_to_method maps the session currency to a method key.
    */

    'methods' => [

        'sa_world' => [
            'country_label' => 'للعملاء في السعودية وبقية دول العالم',
            'type'          => 'bank_transfer',
            'bank_name'     => 'STC Bank',
            'account_name'  => 'محمود عبدالله',
            'account_number'=> '1028992404',
            'iban'          => 'SA7178000000001028992404',
        ],

        'eg' => [
            'country_label' => 'للعملاء في مصر',
            'type'          => 'instapay',
            'link'          => 'https://ipn.eg/S/mindfitbro/instapay/4s2ZPS',
            'instapay_id'   => 'mindfitbro@instapay',
            'phone'         => '01098630291',
        ],

        'tn' => [
            'country_label' => 'للعملاء في تونس',
            'type'          => 'bank_transfer',
            'bank_name'     => 'الشركة التونسية للبنك (STB)',
            'account_name'  => 'Salim Taboubi',
            'rib'           => '10404100144006978896',
            'swift'         => 'STBKTNTT',
        ],

    ],

    'currency_to_method' => [
        'SAR' => 'sa_world',
        'USD' => 'sa_world',
        'EGP' => 'eg',
        'TND' => 'tn',
    ],

];
