<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $validated['type'] = 'contact';

        // Tạo bản ghi lưu vào CSDL
        \App\Models\Contact::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.'
        ]);
    }

    public function subscribeNewsletter(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:contacts,email,NULL,id,type,newsletter',
        ], [
            'email.unique' => 'Email này đã được đăng ký nhận thông báo từ trước.',
        ]);

        $validated['type'] = 'newsletter';

        \App\Models\Contact::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký nhận tin thành công! Cảm ơn bạn.'
        ]);
    }
}
