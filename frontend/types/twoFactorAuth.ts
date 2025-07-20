export interface Setup2FAResponse {
    qrCode: string;
    secret: string;
}

export interface Verify2FAResponse {
    valid: boolean;
    message: string;
}

export interface TwoFactorAuthState {
    isEnabled: boolean;
    secret?: string;
    qrCodeUrl?: string;
    backupCodes?: string[];
}

export interface TwoFactorVerificationProps {
    email?: string;
    onVerificationSuccess?: () => void;
    onCancel?: () => void;
}
