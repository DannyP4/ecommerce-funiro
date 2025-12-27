@extends('customer.layouts.app')

@section('title', __('Contact') . ' - Furniro')

@section('hero')
<section class="hero-section">
    <div class="hero-content">
        <h1>{{ __('Contact') }}</h1>
    </div>
</section>
@endsection

@section('content')
<div class="contact-container">
    <!-- Contact Header -->
    <div class="contact-header">
        <h2>{{ __('Get in Touch with Us') }}</h2>
        <p class="contact-subtitle">{{ __('Have questions about our furniture? Our Team 1 is here to help you!') }}</p>
    </div>

    <!-- Contact Information -->
    <div class="contact-info">
        <div class="contact-grid">
            <!-- Company Info -->
            <div class="contact-section">
                <div class="contact-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3>{{ __('Furniro Furniture') }}</h3>
                <p>{{ __('Premium furniture for your home') }}</p>
                <div class="contact-details">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ __('Hanoi University of Science and Technology') }}</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>+84 (09) 1234 5678</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <span>{{ __('Mon - Fri: 9:00 AM - 6:00 PM') }}</span>
                    </div>
                </div>
            </div>

            <!-- Support -->
            <div class="contact-section">
                <div class="contact-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>{{ __('Customer Support') }}</h3>
                <p>{{ __('Need help with your order or have questions about our products?') }}</p>
                <div class="contact-details">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>support@furniro.com</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone-alt"></i>
                        <span>1900 1234 ({{ __('Free call') }})</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-comments"></i>
                        <span>{{ __('Live Chat Available') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="team-section">
        <h2>{{ __('Meet Me') }}</h2>
        <p class="team-subtitle">{{ __('The developers behind Furniro') }}</p>
        
        <div class="team-grid">
            <div class="team-member">
                <div class="member-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h3>Phạm Đức Long</h3>
                <p class="member-role">{{ __('Backend Developer') }}</p>
                <div class="member-contact">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>long.pd225737@sis.hust.edu.vn</span>
                    </div>
                </div>
                <div class="member-skills">
                    <span class="skill-tag">Laravel</span>
                    <span class="skill-tag">MySQL</span>
                    <span class="skill-tag">API Design</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Info -->
    <div class="project-info">
        <h2>{{ __('About This Project') }}</h2>
        <div class="project-details">
            <div class="project-item">
                <i class="fas fa-code"></i>
                <h4>{{ __('Technology Stack') }}</h4>
                <p>Laravel 10, PHP 8.2, MySQL, Tailwind CSS, JavaScript</p>
            </div>
            <div class="project-item">
                <i class="fas fa-users"></i>
                <h4>{{ __('Team') }}</h4>
                <p>{{ __('36') }}</p>
            </div>
            <div class="project-item">
                <i class="fas fa-calendar"></i>
                <h4>{{ __('Development Period') }}</h4>
                <p>{{ __('Oct 2025 - Present') }}</p>
            </div>
            <div class="project-item">
                <i class="fas fa-github"></i>
                <h4>{{ __('Repository') }}</h4>
                <p>{{ __('Available on GitHub') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.contact-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.contact-header {
    text-align: center;
    margin-bottom: 60px;
}

.contact-header h2 {
    font-size: 36px;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
}

.contact-subtitle {
    font-size: 18px;
    color: #666;
    max-width: 600px;
    margin: 0 auto;
}

.contact-info {
    margin-bottom: 80px;
}

.contact-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 40px;
}

.contact-section {
    background: #fff;
    padding: 40px 30px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.contact-section:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.contact-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #B88E2F, #D4A442);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.contact-icon i {
    font-size: 32px;
    color: white;
}

.contact-section h3 {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

.contact-section > p {
    color: #666;
    margin-bottom: 25px;
}

.contact-details {
    text-align: center;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
    color: #555;
    justify-content: center;
}

.contact-item i {
    color: #B88E2F;
    width: 16px;
    font-size: 14px;
}

/* Team Section */
.team-section {
    background: #F9F1E7;
    padding: 60px 40px;
    border-radius: 15px;
    text-align: center;
    margin-bottom: 60px;
}

.team-section h2 {
    font-size: 32px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

.team-subtitle {
    font-size: 16px;
    color: #666;
    margin-bottom: 50px;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.team-member {
    background: white;
    padding: 30px 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.team-member:hover {
    transform: translateY(-3px);
}

.member-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #B88E2F, #D4A442);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.member-avatar i {
    font-size: 40px;
    color: white;
}

.team-member h3 {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.member-role {
    color: #B88E2F;
    font-weight: 500;
    margin-bottom: 20px;
}

.member-contact {
    text-align: center;
    margin-bottom: 20px;
}

.member-contact .contact-item {
    margin-bottom: 10px;
    font-size: 14px;
    justify-content: center;
}

.member-skills {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
}

.skill-tag {
    background: #B88E2F;
    color: white;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 500;
}

/* Project Info */
.project-info {
    text-align: center;
}

.project-info h2 {
    font-size: 32px;
    font-weight: 600;
    color: #333;
    margin-bottom: 40px;
}

.project-details {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
}

.project-item {
    background: white;
    padding: 25px 20px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.project-item:hover {
    transform: translateY(-3px);
}

.project-item i {
    font-size: 24px;
    color: #B88E2F;
    margin-bottom: 15px;
}

.project-item h4 {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.project-item p {
    color: #666;
    font-size: 14px;
    line-height: 1.5;
}

/* Responsive */
@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }
    
    .team-section {
        padding: 40px 20px;
    }
    
    .team-grid {
        grid-template-columns: 1fr;
    }
    
    .project-details {
        grid-template-columns: 1fr;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .project-details {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
@endpush
