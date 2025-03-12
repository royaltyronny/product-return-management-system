@extends('layouts.app')

@section('content')
<style>
    /* About Page Styling */
    .about-container {
        margin-top: 70px;
        max-width: 900px;
        margin-left: auto;
        margin-right: auto;
        background: #f5f5f5;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    .about-heading {
        color: #00796b;
        text-align: center;
        margin-bottom: 20px;
    }
    .about-content p {
        color: #555;
        line-height: 1.8;
        text-align: justify;
    }
    .about-footer {
        text-align: center;
        margin-top: 30px;
    }
</style>

<div class="about-container">
    <h1 class="about-heading">
        <i class="fas fa-info-circle"></i> About PRMS
    </h1>
    <div class="about-content">
        <p>
            Welcome to the Product Return Management System (PRMS) – your go-to solution for handling product returns with ease and efficiency. 
            PRMS is designed to streamline the return process, track product statuses, and provide insights into return trends, ensuring customer satisfaction and operational excellence.
        </p>
        <p>
            Our system offers an intuitive interface, a comprehensive shoe collection display, and seamless integration with your workflow. 
            Whether you’re managing a large inventory or handling individual customer requests, PRMS simplifies the process, so you can focus on delivering exceptional service.
        </p>
        <p>
            Built with cutting-edge technologies and designed with the user in mind, PRMS empowers businesses to handle returns confidently, reducing errors and improving turnaround time.
        </p>
    </div>
    <div class="about-footer">
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>
@endsection
