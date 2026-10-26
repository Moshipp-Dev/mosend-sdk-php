<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/auth/*` — autenticación (público). */
final class AuthResource extends Resource
{
    /** @param array<string,mixed> $input @return mixed */
    public function login(array $input)
    {
        return $this->http->request('POST', '/auth/login', ['body' => $input]);
    }

    /** @param array<string,mixed> $input @return mixed */
    public function signup(array $input)
    {
        return $this->http->request('POST', '/auth/signup', ['body' => $input]);
    }

    /** @param array<string,mixed> $input refreshToken @return mixed */
    public function refresh(array $input)
    {
        return $this->http->request('POST', '/auth/refresh', ['body' => $input, 'skipAuth' => true]);
    }

    /** @param array<string,mixed> $input refreshToken */
    public function logout(array $input): void
    {
        $this->http->request('POST', '/auth/logout', ['body' => $input]);
    }

    /** @param array<string,mixed> $input email, captchaToken? */
    public function forgotPassword(array $input): void
    {
        $this->http->request('POST', '/auth/forgot-password', ['body' => $input]);
    }

    /** @param array<string,mixed> $input token, password */
    public function resetPassword(array $input): void
    {
        $this->http->request('POST', '/auth/reset-password', ['body' => $input]);
    }

    /** @return mixed */
    public function captchaStatus()
    {
        return $this->http->request('GET', '/auth/captcha-status');
    }

    /** @return mixed */
    public function factorStatus()
    {
        return $this->http->request('GET', '/auth/factor-status');
    }

    /** @param array<string,mixed> $input code @return mixed */
    public function stepUp2fa(array $input)
    {
        return $this->http->request('POST', '/auth/step-up/2fa', ['body' => $input]);
    }

    /** @param array<string,mixed> $input token @return mixed */
    public function verifyEmail(array $input)
    {
        return $this->http->request('POST', '/auth/verify-email', ['body' => $input]);
    }

    /** @return mixed */
    public function resendVerification()
    {
        return $this->http->request('POST', '/auth/resend-verification');
    }

    /** @param array<string,mixed> $input token @return mixed */
    public function impersonateRedeem(array $input)
    {
        return $this->http->request('POST', '/auth/impersonate-redeem', ['body' => $input]);
    }

    /** @return mixed */
    public function stepUpPasskeyOptions()
    {
        return $this->http->request('POST', '/auth/step-up/passkey/options');
    }

    /** @param array<string,mixed> $input challengeKey, response @return mixed */
    public function stepUpPasskeyVerify(array $input)
    {
        return $this->http->request('POST', '/auth/step-up/passkey/verify', ['body' => $input]);
    }

    /**
     * Sesiones activas del usuario (una por dispositivo), marcando la actual. Requiere sesión JWT del panel.
     * @return mixed
     */
    public function listSessions()
    {
        return $this->http->request('GET', '/auth/me/sessions');
    }

    /**
     * Cierra las demás sesiones del usuario y conserva la actual. Requiere sesión JWT del panel.
     * @return mixed
     */
    public function closeOtherSessions()
    {
        return $this->http->request('DELETE', '/auth/me/sessions');
    }

    /**
     * Cierra una sesión propia (revoca toda su cadena de refresh). Requiere sesión JWT del panel.
     * @return mixed
     */
    public function closeSession(string $id)
    {
        return $this->http->request('DELETE', "/auth/me/sessions/{$id}");
    }

    /**
     * Flags públicos del registro (verificación de teléfono / correo).
     * @return mixed
     */
    public function signupConfig()
    {
        return $this->http->request('GET', '/auth/signup/config');
    }

    /**
     * Registro con teléfono verificado, paso 1: envía el código OTP por WhatsApp.
     *
     * @param array<string,mixed> $input email, password, name, phone, captchaToken?
     * @return mixed
     */
    public function signupRequestCode(array $input)
    {
        return $this->http->request('POST', '/auth/signup/request-code', ['body' => $input, 'skipAuth' => true]);
    }

    /**
     * Reenvía el código OTP del registro pendiente.
     *
     * @param array<string,mixed> $input email, phone
     * @return mixed
     */
    public function signupResendCode(array $input)
    {
        return $this->http->request('POST', '/auth/signup/resend-code', ['body' => $input, 'skipAuth' => true]);
    }

    /**
     * Registro, paso 2: verifica el código, crea la cuenta y emite tokens.
     *
     * @param array<string,mixed> $input email, phone, code
     * @return mixed
     */
    public function signupVerifyCode(array $input)
    {
        return $this->http->request('POST', '/auth/signup/verify-code', ['body' => $input, 'skipAuth' => true]);
    }
}
