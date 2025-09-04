@extends('customer.layouts.app')

@section('title', __('About') . ' - Furniro')

@section('hero')
<section class="hero-section">
    <div class="hero-content">
        <h1>{{ __('About') }}</h1>
    </div>
</section>
@endsection

@section('content')
<div class="about-container">
    <!-- About Header -->
    <div class="about-header">
        <h2>{{ __('Welcome to Furniro') }}</h2>
        <p class="about-subtitle">{{ __('Your Premium Furniture Destination') }}</p>
    </div>

    <!-- About Content -->
    <div class="about-content">
        <div class="about-grid">
            <!-- Our Story -->
            <div class="about-section">
                <div class="about-icon">
                    <i class="fas fa-home"></i>
                </div>
                <h3>{{ __('Our Story') }}</h3>
                <p>{{ __('Furniro was founded with a simple mission: to bring beautiful, high-quality furniture to homes around the world. We believe that every space deserves furniture that combines style, comfort, and durability.') }}</p>
            </div>

            <!-- Our Mission -->
            <div class="about-section">
                <div class="about-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h3>{{ __('Our Mission') }}</h3>
                <p>{{ __('We are committed to providing exceptional furniture that transforms houses into homes. Our carefully curated collection features modern designs, premium materials, and craftsmanship that stands the test of time.') }}</p>
            </div>

            <!-- Quality Promise -->
            <div class="about-section">
                <div class="about-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>{{ __('Quality Promise') }}</h3>
                <p>{{ __('Every piece in our collection undergoes rigorous quality testing. We partner with skilled craftsmen and trusted manufacturers to ensure that each furniture item meets our high standards for durability and design.') }}</p>
            </div>

            <!-- Customer First -->
            <div class="about-section">
                <div class="about-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h3>{{ __('Customer First') }}</h3>
                <p>{{ __('Your satisfaction is our priority. We provide excellent customer service, easy returns, and ongoing support to ensure you love your furniture for years to come.') }}</p>
            </div>
        </div>
    </div>

    <!-- Features -->
    <div class="about-features">
        <h2>{{ __('Why Choose Furniro?') }}</h2>
        <div class="features-grid">
            <div class="feature-item">
                <i class="fas fa-truck"></i>
                <h4>{{ __('Free Delivery') }}</h4>
                <p>{{ __('Free shipping on orders over 1,000,000 VND') }}</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-undo"></i>
                <h4>{{ __('Easy Returns') }}</h4>
                <p>{{ __('30-day return policy for your peace of mind') }}</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-tools"></i>
                <h4>{{ __('Expert Assembly') }}</h4>
                <p>{{ __('Professional assembly service available') }}</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-award"></i>
                <h4>{{ __('Quality Guarantee') }}</h4>
                <p>{{ __('2-year warranty on all furniture items') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.about-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.about-header {
    text-align: center;
    margin-bottom: 60px;
}

.about-header h2 {
    font-size: 36px;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
}

.about-subtitle {
    font-size: 18px;
    color: #666;
    max-width: 600px;
    margin: 0 auto;
}

.about-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 40px;
    margin-bottom: 80px;
}

.about-section {
    text-align: center;
    padding: 30px 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.about-section:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.about-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #B88E2F, #D4A442);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.about-icon i {
    font-size: 32px;
    color: white;
}

.about-section h3 {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
}

.about-section p {
    color: #666;
    line-height: 1.6;
    font-size: 16px;
}

.about-features {
    background: #F9F1E7;
    padding: 60px 40px;
    border-radius: 15px;
    text-align: center;
}

.about-features h2 {
    font-size: 32px;
    font-weight: 600;
    color: #333;
    margin-bottom: 50px;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}

.feature-item {
    background: white;
    padding: 30px 20px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.feature-item:hover {
    transform: translateY(-3px);
}

.feature-item i {
    font-size: 28px;
    color: #B88E2F;
    margin-bottom: 15px;
}

.feature-item h4 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

.feature-item p {
    color: #666;
    font-size: 14px;
    line-height: 1.5;
}

/* Responsive */
@media (max-width: 768px) {
    .about-header h2 {
        font-size: 28px;
    }
    
    .about-grid {
        grid-template-columns: 1fr;
    }
    
    .about-features {
        padding: 40px 20px;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush
