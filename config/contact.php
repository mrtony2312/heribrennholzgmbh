<?php

return [

    // WhatsApp-Nummer im internationalen Format OHNE "+", Leerzeichen oder Bindestriche.
    // Beispiel Schweiz: 41791234567
    'whatsapp' => env('CONTACT_WHATSAPP', ''),

    // Vordefinierter Text, der im WhatsApp-Chat vorausgefüllt wird.
    'whatsapp_text' => env('CONTACT_WHATSAPP_TEXT', 'Guten Tag, ich habe eine Frage zu Ihren Produkten.'),
];
