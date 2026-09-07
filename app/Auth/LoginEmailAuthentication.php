<?php

namespace App\Auth;

use App\Support\DynamicMailSettings;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class LoginEmailAuthentication extends EmailAuthentication
{
    public function sendCode(HasEmailAuthentication $user): bool
    {
        try {
            DynamicMailSettings::apply(purgeResolvedMailers: true);

            return parent::sendCode($user);
        } catch (Throwable $exception) {
            Log::warning('Login OTP email could not be sent.', [
                'user_id' => method_exists($user, 'getKey') ? $user->getKey() : null,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    public function beforeChallenge(Authenticatable $user): void
    {
        if (! $user instanceof HasEmailAuthentication) {
            parent::beforeChallenge($user);

            return;
        }

        if ($this->sendCode($user)) {
            return;
        }

        throw ValidationException::withMessages([
            'data.email' => 'OTP email could not be sent right now. Please try again shortly or contact the administrator.',
        ]);
    }
}
