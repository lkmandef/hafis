import jsQR from 'jsqr';

// Expose jsQR to window for QR decoding fallback specifically on scanner page
window.jsQR = jsQR;
