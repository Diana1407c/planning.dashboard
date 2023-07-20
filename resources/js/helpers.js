export default function errorMessages(response) {
    let messages = []
    if (response.status === 422) {
        Object.values(response.data.errors).map(( value, key, index) => {
            if (Array.isArray(value)) {
                for (let i=0; i<value.length; i++) {
                    messages.push(value[i]);
                }
            } else {
                messages.push(value);
            }
        })
    }else if(response.data.hasOwnProperty('message')) {
        messages.push(response.data.message);
    } else {
        messages.push('unknown error');
    }

    return messages;
}
