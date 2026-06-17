<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactFormController extends Controller
{
    public function show(): View
    {
        return view('contact-form');
    }

    public function submit(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns'],
            'message' => ['nullable', 'string', 'max:5000'],
        ], [
            'email.required' => 'Поле Email є обов\'язковим для заповнення.',
            'email.email' => 'Вкажіть коректну адресу електронної пошти.',
            'name.max' => 'Ім\'я не може бути довшим за :max символів.',
            'message.max' => 'Повідомлення не може бути довшим за :max символів.',
        ]);

        Mail::to('6weeks.13h@gmail.com')->send(new ContactFormMail($validated));
        Mail::to('lucifer.dragon.19999@gmail.com')->send(new ContactFormMail($validated));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Дякуємо! Ваше повідомлення успішно надіслано.']);
        }

        return redirect()->route('contact-form.show')->with('success', 'Дякуємо! Ваше повідомлення успішно надіслано.');
    }
}
