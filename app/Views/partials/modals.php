<!-- Registration Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark text-light rounded-3 border-0 shadow-lg">
      <button type="button" class="btn-close position-absolute end-0 mt-3 me-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1050;">
      </button>
      
      <!-- Modal Header -->
      <div class="modal-header border-0 flex-column align-items-center pt-4 pb-1">
        <div class="text-center mb-2">
          <div class="bg-gradient rounded-circle p-3 mb-2" style="background: linear-gradient(135deg, var(--primary-purple), var(--accent-color)); width: 60px; height: 60px; display: inline-flex; align-items: center; justify-content: center;">
            <i class="fas fa-user-plus text-white" style="font-size: 1.5rem;"></i>
          </div>
        </div>
        <h4 class="modal-title w-100 text-center fw-bold mb-1" id="registerModalLabel">Create Your Account</h4>
        <p class="text-muted text-center mb-0 small">Join our amazing anime community and discover your next favorite series</p>
      </div>
      
      <!-- Modal Body -->
      <div class="modal-body pt-2 pb-3 px-4">
        <form id="registerForm" autocomplete="off">
          <!-- CSRF Token -->
          <?php if (function_exists('csrf_field')): ?>
            <?= csrf_field() ?>
          <?php endif; ?>
          
          <div class="row g-3">
            <!-- Left Column - Personal Information -->
            <div class="col-md-6">
              <div class="border-end border-secondary pe-md-3">
                <h6 class="text-uppercase text-muted mb-3 fw-bold">
                  <i class="fas fa-user-circle me-2"></i>Personal Information
                </h6>
                
                <div class="mb-3">
                  <label for="registerUsername" class="form-label mb-1">
                    <i class="fas fa-user me-2 text-primary"></i>Username
                  </label>
                  <input type="text" class="form-control form-control-lg rounded-3" id="registerUsername" name="username" placeholder="Choose a unique username" required>
                  <div class="invalid-feedback"></div>
                  <small class="text-muted">This will be your unique identifier</small>
                </div>
                
                <div class="mb-3">
                  <label for="registerDisplayName" class="form-label mb-1">
                    <i class="fas fa-id-card me-2 text-info"></i>Display Name
                  </label>
                  <input type="text" class="form-control form-control-lg rounded-3" id="registerDisplayName" name="display_name" placeholder="How others will see you" required>
                  <div class="invalid-feedback"></div>
                  <small class="text-muted">Your friendly name in the community</small>
                </div>
                
                <div class="mb-2">
                  <label for="registerEmail" class="form-label mb-1">
                    <i class="fas fa-envelope me-2 text-warning"></i>Email Address
                  </label>
                  <input type="email" class="form-control form-control-lg rounded-3" id="registerEmail" name="email" placeholder="your@email.com" required>
                  <div class="invalid-feedback"></div>
                  <small class="text-muted">We'll send updates and notifications here</small>
                </div>
              </div>
            </div>
            
            <!-- Right Column - Security -->
            <div class="col-md-6">
              <div class="ps-md-3">
                <h6 class="text-uppercase text-muted mb-3 fw-bold">
                  <i class="fas fa-shield-alt me-2"></i>Account Security
                </h6>
                
                <div class="mb-3">
                  <label for="registerPassword" class="form-label mb-1">
                    <i class="fas fa-lock me-2 text-danger"></i>Password
                  </label>
                  <input type="password" class="form-control form-control-lg rounded-3" id="registerPassword" name="password" placeholder="Create a strong password" required>
                  <div class="invalid-feedback"></div>
                  <small class="text-muted">At least 8 characters with mixed case and numbers</small>
                </div>
                
                <div class="mb-3">
                  <label for="registerConfirmPassword" class="form-label mb-1">
                    <i class="fas fa-lock me-2 text-danger"></i>Confirm Password
                  </label>
                  <input type="password" class="form-control form-control-lg rounded-3" id="registerConfirmPassword" name="confirm_password" placeholder="Confirm your password" required>
                  <div class="invalid-feedback"></div>
                  <small class="text-muted">Make sure both passwords match</small>
                </div>
                
                <!-- Terms and Privacy -->
                <div class="mb-2">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                    <label class="form-check-label small" for="agreeTerms">
                      I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and <a href="#" class="text-decoration-none">Privacy Policy</a>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Error Display -->
          <div class="alert alert-danger d-none mt-3" id="registerFormErrors"></div>
          
          <!-- Submit Button -->
          <div class="row mt-3">
            <div class="col-12">
              <button type="submit" class="btn btn-primary btn-lg w-100 py-2 rounded-3 fw-bold">
                <i class="fas fa-user-plus me-2"></i>Create My Account
                <i class="fas fa-arrow-right ms-2"></i>
              </button>
            </div>
          </div>
        </form>
        
        <!-- Footer -->
        <div class="text-center mt-3 pt-2 border-top border-secondary">
          <p class="mb-0">
            Already have an account? 
            <a href="#" class="login-link text-decoration-none fw-bold">
              <i class="fas fa-sign-in-alt me-1"></i>Sign In Instead
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content bg-dark text-light rounded-3 border-0 shadow-lg">
      <button type="button" class="btn-close position-absolute end-0 mt-3 me-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1050;">
      </button>
      
      <!-- Modal Header -->
      <div class="modal-header border-0 flex-column align-items-center pt-4 pb-2">
        <div class="text-center mb-3">
          <div class="bg-gradient rounded-circle p-3 mb-2" style="background: linear-gradient(135deg, var(--primary-purple), var(--accent-color)); width: 70px; height: 70px; display: inline-flex; align-items: center; justify-content: center;">
            <i class="fas fa-sign-in-alt text-white" style="font-size: 1.8rem;"></i>
          </div>
        </div>
        <h4 class="modal-title w-100 text-center fw-bold mb-2" id="loginModalLabel">Welcome Back</h4>
        <p class="text-muted text-center mb-0">Sign in to continue your anime journey</p>
      </div>
      
      <!-- Modal Body -->
      <div class="modal-body pt-2 pb-4 px-4">
        <form id="loginForm" autocomplete="off">
          <div class="alert alert-danger d-none" id="loginFormErrors"></div>
          
          <div class="mb-4">
            <label for="loginEmail" class="form-label mb-2">
              <i class="fas fa-envelope me-2 text-warning"></i>Email Address
            </label>
            <input type="email" class="form-control form-control-lg rounded-3" id="loginEmail" name="email" placeholder="Enter your email address" required>
            <small class="text-muted">Use the email you registered with</small>
          </div>
          
          <div class="mb-4">
            <label for="loginPassword" class="form-label mb-2">
              <i class="fas fa-lock me-2 text-danger"></i>Password
            </label>
            <input type="password" class="form-control form-control-lg rounded-3" id="loginPassword" name="password" placeholder="Enter your password" required>
            <small class="text-muted">Your secure password</small>
          </div>
          
          <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
              <label class="form-check-label" for="rememberMe">
                <i class="fas fa-heart me-1"></i>Keep me signed in
              </label>
            </div>
            <a href="#" class="text-decoration-none fw-bold">
              <i class="fas fa-key me-1"></i>Forgot Password?
            </a>
          </div>
          
          <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-3 fw-bold mb-3">
            <i class="fas fa-sign-in-alt me-2"></i>Sign In
            <i class="fas fa-arrow-right ms-2"></i>
          </button>
        </form>
        
        <!-- Footer -->
        <div class="text-center pt-3 border-top border-secondary">
          <p class="mb-0">
            Don't have an account? 
            <a href="#" class="register-link text-decoration-none fw-bold">
              <i class="fas fa-user-plus me-1"></i>Create One Now
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
