<?php

declare(strict_types=1);

/*
 * Planned HTTP route map for the Laragon deployment. Current public entry
 * files remain in place for backward-compatible prototype access.
 */
return [
    '/' => ['GET', 'LandingController@show'],
    '/receptionist' => ['GET', 'ReceptionController@show'],
    '/public-display' => ['GET', 'PublicDisplayController@show'],
    '/api/queue' => ['GET', 'QueueController@publicFeed'],
    '/api/tickets' => ['POST', 'TicketController@store'],
    '/api/reports/export' => ['GET', 'ReportController@export'],
];
