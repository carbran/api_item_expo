<?php

namespace App\Enum;

enum SizeTypeEnum: string {
    const PAGES  = 0;
    const INCHES = 1;
    const LH  = 2; // comprimento x altura em cm
    const LBH = 3; // comprimento x largura x altura em cm
}
