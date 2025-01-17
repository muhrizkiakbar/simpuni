<?php

namespace App\Enums;

enum DenunciationEnum: string
{
    case Sent = 'sent';
    case Cancel = 'cancel';
    case TeguranLisan = 'teguran_lisan';
    case Sp1 = 'sp1';
    case Sp2 = 'sp2';
    case Sp3 = 'sp3';
    case Sk_Bongkar = 'sk_bongkar';
    case Selesai = 'done';
}
