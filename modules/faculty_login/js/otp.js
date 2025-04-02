const otpInputs = document.querySelectorAll('.otp-box');

otpInputs.forEach((input, index) => {
    input.addEventListener('input', () => {
        if (input.value.length == 1 && index < otpInputs.length - 1) {
            // Move to the next input if this isn't the last one
            otpInputs[index + 1].focus();
        }
    });

    input.addEventListener('keydown', (e) => {
        if (e.key == 'Backspace' && input.value.length == 0 && index > 0) {
            // If Backspace is pressed and the input is empty, go to the previous input
            otpInputs[index - 1].focus();
        }
    });
});