<?php

return [

    // We recommend keeping the default settings
    // We also recommend that you do not change the settings after you have started your ladder
    'settings' => [

        // The rating new players start at
        'start_rating'  => 1000,

        'glicko2' => [
            /**
             * Volatility determines how much the rating changes each game
             * The optimal volatility value depends on how many games you play
             *
             * Some guidelines based on games per ladder period and player:
             * < 2: < 0.05
             * 2-5: 0.05 - 0.07
             * > 5: < 0.07+
             */
            'volatility'            => 0.06,
            // Rating deviation, constrains volatility change over time
            'rating_deviation'      => 350,
            //
            'conversion_multiplier' => 173.7178
        ],
    ],

    'ladders' => [
        // Key for period(must be unique)
        'weekly' => [
            'label'         => 'Weekly',
            // Period: weekly, monthly, yearly or alltime
            'period'        => 'weekly',
            // Number of weeks/months/years in period. Default: 1
            'interval'      => 1,
            // ISO-8601 numeric representation of the day or 'last' for last day of period
            // 1 for first day n for nth day. Default: 1
            'reset_day'     => 1,
            // Time for ladder reset
            'reset_time'    => '00:00:00'
        ],
        'monthly' => [
            'label'         => 'Monthly',
            'period'        => 'monthly',
            'interval'      => 1,
            'reset_day'     => 1,
            'reset_time'    => '00:00:00'
        ],
        'all_time' => [
            'label'     => 'All time',
            'period'    => 'alltime'
        ]
    ]


];
