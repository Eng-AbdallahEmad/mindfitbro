<?php

return [
    /*
     * Minimum number of confirmed subscriptions (approved|active|expired) a plan
     * must have before the "most popular" badge is assigned automatically.
     * Below this threshold the badge falls back to the admin's manual selection.
     */
    'popular_min_subscriptions' => 5,
];
