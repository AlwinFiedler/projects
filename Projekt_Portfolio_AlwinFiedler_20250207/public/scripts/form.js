const form = document.getElementById('form');
const userName = document.getElementById('username');
const phoneNumber = document.getElementById('phonenumber');
const emailAddress = document.getElementById('emailaddress');
const textMessage = document.getElementById('textmessage');

const clearInputFields = () => {
    const inputs = document.querySelectorAll('input[type="text"]');

    inputs.forEach(input => {
        input.value = '';
    });

    textMessage.value = '';
}

form.addEventListener('submit', async e => {
    e.preventDefault(); 
    validateInputs();

    const errors = document.querySelectorAll('.error');
    const hasErrors = Array.from(errors).some(error => error.innerText !== '');

    if (!hasErrors) {
        const formData = {
            username: userName.value.trim(),
            phonenumber: phoneNumber.value.trim(),
            emailaddress: emailAddress.value.trim(),
            textmessage: textMessage.value.trim(),
        };

        try {
            const response = await fetch('/submit-form', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            });

            if (response.ok) {
                const result = await response.text();
                console.log(result);
                alert('Form submitted successfully!');
                clearInputFields();
            } else {
                const errorMessage = await response.text();
                console.error('Error:', errorMessage);
                alert('An error occurred while submitting the form.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while submitting the form.');
        }
    }
});

const setSuccess = element => {
    const inputControl = element.parentElement;
    const errorDisplay = inputControl.querySelector('.error');

    errorDisplay.innerText = '';
    inputControl.classList.add('success');
    inputControl.classList.remove('error');
}

const setError = (element, message) => {
    const inputControl = element.parentElement;
    const errorDisplay = inputControl.querySelector('.error');

    errorDisplay.innerText = message;
    inputControl.classList.add('error');
    inputControl.classList.remove('success');
}

const hasNumbers = /\d/;

const isValidEmailAddress = email => {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

const isValidPhoneNumber = phone => {
    const re = /^\+?\d.\s?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}?/;
    return re.test(String(phone));
}

const validateInputs = () => {
    const userNameValue = userName.value.trim();
    const phoneNumberValue = phoneNumber.value.trim();
    const emailAddressValue = emailAddress.value.trim();

    if (userNameValue === '') {
        setError(userName, 'Name ist Pflichtfeld, bitte Namen eintragen.');
    } else if (hasNumbers.test(userNameValue)) {
        setError(userName, 'Bitte einigermaßen seriösen Namen eintragen.');
    } else {
        setSuccess(userName);
    }

    if (phoneNumberValue !== '' && !isValidPhoneNumber(phoneNumberValue)) {
        setError(phoneNumber, 'Bitte eine gültige Mobilnummer eintragen.');
    } else {
        setSuccess(phoneNumber);
    }

    if (emailAddressValue === '') {
        setError(emailAddress, 'E-Mail ist Pflichtfeld, bitte E-Mail eintragen.');
    } else if (!isValidEmailAddress(emailAddressValue)) {
        setError(emailAddress, 'Bitte eine gültige E-Mail-Adresse eintragen.');
    } else {
        setSuccess(emailAddress);
    }
}
clearInputFields();