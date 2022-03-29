@extends('layouts.app')

@section('content')
    <div class="flex flex-col justify-center min-h-screen py-12 bg-gray-50 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center">
            <div class="flex flex-col justify-around">
                <div class="space-y-6">
                    
                    <form method="POST" action="{{ route('admin.config.post') }}">
                        @csrf
                        Password: 
                        <br/>
                        <input type="password" name="password">
                        <br/>
                        <input type="submit" name="submit">
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
@endsection
