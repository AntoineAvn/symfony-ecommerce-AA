# symfony-ecommerce-AA

## Ne pas oublier de changer l'url dans src\Controller\StripeController.php

#### Dans la fonction checkoutCreate():
###### Lors de la création de la session Stripe, on a 2 url prédéfinie en cas de success/cancel, la redirection doit être sur une url http ou https donc à changer en fonction de l'url de notre serveur local

### Exemple: 
      'success_url' => 'http://127.0.0.1:8000/stripe/success',
      'cancel_url' => 'http://127.0.0.1:8000/stripe/cancel',
