<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum MailEvent: string implements HasColor, HasIcon, HasLabel
{
    case Queued = 'queued';         // requested + pushed onto the queue
    case Processing = 'processing'; // a worker picked the job up
    case Sending = 'sending';       // the SMTP/Mailgun transport is firing
    case Sent = 'sent';             // the transport accepted the message
    case Processed = 'processed';   // the queue job finished cleanly
    case Retrying = 'retrying';     // the job threw and will be retried
    case Failed = 'failed';         // the job exhausted its tries

    public function getLabel(): string
    {
        return __('enums.mail_event.'.$this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Queued     => 'gray',
            self::Processing => 'info',
            self::Sending    => 'info',
            self::Sent       => 'success',
            self::Processed  => 'success',
            self::Retrying   => 'warning',
            self::Failed     => 'danger',
        };
    }

    public function getIcon(): string|BackedEnum|null
    {
        return match ($this) {
            self::Queued     => Heroicon::OutlinedInbox,
            self::Processing => Heroicon::OutlinedCog6Tooth,
            self::Sending    => Heroicon::OutlinedPaperAirplane,
            self::Sent       => Heroicon::OutlinedCheckCircle,
            self::Processed  => Heroicon::OutlinedFlag,
            self::Retrying   => Heroicon::OutlinedArrowPath,
            self::Failed     => Heroicon::OutlinedXCircle,
        };
    }
}
