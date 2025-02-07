<?php

/*
 * This is the status codes file where all the app constants are saved.
 */

return [
    'en' => [
        'success_messages' => [
            'LOGIN' => 'Logged In',
            'ADD_GOV' => 'Governerate was created Successfully',
            'RETRIEVED_GOV' => 'Successfully retrieved Governerate',
            'ADD_REGION' => 'Region was created Successfully',
            'RETRIEVED_REGION' => 'Successfully retrieved regions',
            'ADD_VILLAGE' => 'Village was created Successfully',
            'RETRIEVED_VILLAGE' => 'Successfully retrieved village',
            'RETRIEVED_FARMER' => 'Successfully retrieved farmers',
            'ADD_CONTAINER' => 'Container was created Successfully',
            'RETRIEVED_CONTAINER' => 'Successfully retrieved containera',
            'RETRIEVED_TRANSACTION' => 'Successfully retrieved transactions',
            'RETRIEVED_TRANSACTION_DETAIL' => 'Successfully retrieved transactions details',
            'RETRIEVED_BATCHES' => 'Successfully retrieved batches',
            'ADD_FARMER' => 'Farmer was created Successfully',
            'ADD_COFFEE' => 'Coffee was added Successfully',
            'ADD_BATCHES' => 'Batches was created Successfully',
            'RETRIEVED_CENTER' => 'Successfully retrieved centers',
            'APPROVED_FARMER' => 'Farmer approved successfully',
            'SENT_COFFEE' => 'Coffee sent successfully',
            'RECV_COFFEE' => 'Transactions received successfully',
            'CENTER_MANAGER_RECV_COFFEE' => 'Center manager received coffee',
            'RECV_COFFEE_MESSAGE' => 'Coffee received',
            'ROLE' => 'Role',
            'PROCESS_LIST' => 'Successfully retrieved processes list',
            'YEAST_LIST' => 'Successfully retrieved processes list',
            'SYSTEM_DEFINATION_REC' => 'Successfully received system defination'
        ],
        // ---------------------- :::::::::::::::::::: ------------------//
        // ---------------------- END - SUCCESS CODES & MESSAGES --------//
        // ---------------------- :::::::::::::::::::: ------------------//
        // ::::::::::: ERROR CODES & MESSAGES :::::::::::: //
        'error_messages' => [
            'BLOCKED' => 'You are blocked by admin',
            'INVALID_USER' => 'Invalid email or password',
            'SESSION_EXPIRED' => 'Session Expired',
            'TRANSACTION_SENT_ALREADY' => 'Some transactions have already been sent',
            'TRANSACTION_REC_ALREADY' => 'Some transactions have already been received',
        ],
    ],
    'ar' => [
        'success_messages' => [
            'LOGIN' => 'تم تسجيل الدخول',
            'ADD_GOV' => 'تم اضافة محافظة',
            'RETRIEVED_GOV' => 'تم استرجاع المحافظة',
            'ADD_REGION' => 'تم إضافة منطقة',
            'RETRIEVED_REGION' => 'تم استرجاع منطقة',
            'ADD_VILLAGE' => 'تم اضافة قرية',
            'RETRIEVED_VILLAGE' => 'تم استرجاع قرية',
            'RETRIEVED_FARMER' => 'تم استرجاع المزارع',
            'ADD_CONTAINER' => 'تم اضافة حاوية',
            'RETRIEVED_CONTAINER' => 'تم استرجاع حاوية',
            'RETRIEVED_TRANSACTION' => 'تم اضافة عملية',
            'RETRIEVED_TRANSACTION_DETAIL' => 'تم استرجاع تفاصيل العملية',
            'RETRIEVED_BATCHES' => 'تم استرجاع الدفعة',
            'ADD_FARMER' => 'تم اضافة مزارع',
            'ADD_COFFEE' => 'تم اضافة البن',
            'ADD_BATCHES' => 'تم إضافة الدفعة ',
            'RETRIEVED_CENTER' => 'تم استرجاع المركز',
            'APPROVED_FARMER' => 'تم الموافقة على المزارع',
            'SENT_COFFEE' => 'تم الإرسال بنجاح',
            'RECV_COFFEE' => 'تم استقبال بيانات العمليات',
            'CENTER_MANAGER_RECV_COFFEE' => 'مدير المركز قام باستقبال البن',
            'RECV_COFFEE_MESSAGE' => 'تم استقبال البن',
            'ROLE' => 'دور',
            'PROCESS_LIST' => 'تم استرجاع بيانات المعالجة',
            'YEAST_LIST' => 'Successfully retrieved yeast list',
            'SYSTEM_DEFINATION_REC' => 'تلقى تعريف النظام بنجاح'
        ],
        // ---------------------- :::::::::::::::::::: ------------------//
        // ---------------------- END - SUCCESS CODES & MESSAGES --------//
        // ---------------------- :::::::::::::::::::: ------------------//
        // ::::::::::: ERROR CODES & MESSAGES :::::::::::: //
        'error_messages' => [
            'BLOCKED' => 'تم تجميد هذا الحساب',
            'INVALID_USER' => 'البريد الاكتروني او كلمة المرور غير صحيحة',
            'SESSION_EXPIRED' => 'انتهت الجلسة',
            'TRANSACTION_SENT_ALREADY' => 'بعض من العمليات قد تم ارسالها',
            'TRANSACTION_REC_ALREADY' => 'بعض من العمليات قد تم استقبالها',
        ],
    ]
];
