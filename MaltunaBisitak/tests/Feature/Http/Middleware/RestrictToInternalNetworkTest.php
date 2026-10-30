<?php

it('forbids the staff area from outside the configured networks', function () {
    config(['bisitak.internal_networks' => ['10.0.0.0/8']]);

    $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.5'])
        ->get(route('login'))
        ->assertForbidden();
});

it('allows the staff area from a configured network', function () {
    config(['bisitak.internal_networks' => ['10.0.0.0/8', '203.0.113.7']]);

    $this->withServerVariables(['REMOTE_ADDR' => '10.20.30.40'])
        ->get(route('login'))
        ->assertOk();
});

it('allows every address when no network is configured', function () {
    config(['bisitak.internal_networks' => []]);

    $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.1'])
        ->get(route('login'))
        ->assertOk();
});
