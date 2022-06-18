<?php

return [
    'default' => 'local',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => sprintf('%s/.config/ovhcli', getenv('HOME')),
        ],
    ],
];
