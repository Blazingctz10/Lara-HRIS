<?php

namespace App\Support;

use App\Models\User;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class BprAvatarProvider implements AvatarProvider
{
    /**
     * Dapatkan avatar profil secara instan tanpa ketergantungan koneksi internet luar (Zero-Latency).
     */
    public function get(Model | Authenticatable $record): string
    {
        if ($record instanceof User) {
            return $record->getFilamentAvatarUrl() ?? User::generateInitialsAvatarSvg($record->name);
        }

        if (method_exists($record, 'getFilamentAvatarUrl') && ($url = $record->getFilamentAvatarUrl())) {
            return $url;
        }

        $name = $record->name ?? $record->full_name ?? 'BPR';

        return User::generateInitialsAvatarSvg($name);
    }
}

