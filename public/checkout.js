// This is your test secret API key.
const stripe = Stripe("pk_test_51Q7ICFKrr8S24r6DyuJFRKy8KgNQ1SqHqQkbY3kvDnaALUnOA8GVubYnAy2WGlVcDjsQZR4uVBJtlWRT4lsyWx6l009qqu4WRG");

initialize();

// Create a Checkout Session
async function initialize() {
    const fetchClientSecret = async () => {
        const response = await fetch("/embedded-form/checkout", {
            method: "POST",
        });
        // console.log('response: ', response);
        // console.log('response j: ', response.json());
        const { clientSecret } = await response.json();
        return clientSecret;
    };

    const checkout = await stripe.initEmbeddedCheckout({
        fetchClientSecret,
    });

    // Mount Checkout
    checkout.mount('#checkout');
}
