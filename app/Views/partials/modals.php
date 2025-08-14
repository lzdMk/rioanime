<!-- Registration Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content bg-dark text-light rounded-3 border-0 shadow-lg">
      <button type="button" class="btn-close position-absolute end-0 mt-2 me-2" data-bs-dismiss="modal" aria-label="Close">
      </button>
      <div class="modal-header border-0 flex-column align-items-center pt-3 pb-0">
        <div class="text-center mb-2">
          <i class="fas fa-user-plus text-purple mb-2" style="font-size: 2rem; color: var(--primary-purple);"></i>
        </div>
        <h5 class="modal-title w-100 text-center fw-bold mb-1" id="registerModalLabel">Create Account</h5>
        <p class="text-muted text-center small mb-0">Join the anime community</p>
      </div>
      <div class="modal-body pt-1 pb-3 px-3">
        <form id="registerForm" autocomplete="off">
          <!-- CSRF Token -->
          <?php if (function_exists('csrf_field')): ?>
            <?= csrf_field() ?>
          <?php endif; ?>
          <div class="mb-2">
            <label for="registerUsername" class="form-label mb-1">
              <i class="fas fa-user me-1"></i>Username
            </label>
            <input type="text" class="form-control rounded-2" id="registerUsername" name="username" placeholder="Choose a username" required>
            <div class="invalid-feedback"></div>
          </div>
          <div class="mb-2">
            <label for="registerDisplayName" class="form-label mb-1">
              <i class="fas fa-id-card me-1"></i>Display Name
            </label>
            <input type="text" class="form-control rounded-2" id="registerDisplayName" name="display_name" placeholder="Your display name" required>
            <div class="invalid-feedback"></div>
          </div>
          <div class="mb-2">
            <label for="registerEmail" class="form-label mb-1">
              <i class="fas fa-envelope me-1"></i>Email
            </label>
            <input type="email" class="form-control rounded-2" id="registerEmail" name="email" placeholder="your@email.com" required>
            <div class="invalid-feedback"></div>
          </div>
          <div class="mb-2">
            <label for="registerPassword" class="form-label mb-1">
              <i class="fas fa-lock me-1"></i>Password
            </label>
            <input type="password" class="form-control rounded-2" id="registerPassword" name="password" placeholder="Create password" required>
            <div class="invalid-feedback"></div>
          </div>
          <div class="mb-2">
            <label for="registerConfirmPassword" class="form-label mb-1">
              <i class="fas fa-lock me-1"></i>Confirm Password
            </label>
            <input type="password" class="form-control rounded-2" id="registerConfirmPassword" name="confirm_password" placeholder="Confirm password" required>
            <div class="invalid-feedback"></div>
          </div>
          <div class="alert alert-danger d-none" id="registerFormErrors"></div>
          <button type="submit" class="btn btn-primary w-100 py-2 rounded-2 fw-bold">
            <i class="fas fa-user-plus me-2"></i>Create Account
          </button>
        </form>
        <div class="text-center mt-2">
          <small>Have an account? <a href="#" class="login-link">Login</a></small>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content bg-dark text-light rounded-3 border-0 shadow-lg">
      <button type="button" class="btn-close position-absolute end-0 mt-2 me-2" data-bs-dismiss="modal" aria-label="Close">
      </button>
      <div class="modal-header border-0 flex-column align-items-center pt-3 pb-0">
        <div class="text-center mb-2">
          <i class="fas fa-sign-in-alt text-purple mb-2" style="font-size: 2rem; color: var(--primary-purple);"></i>
        </div>
        <h5 class="modal-title w-100 text-center fw-bold mb-1" id="loginModalLabel">Welcome Back</h5>
        <p class="text-muted text-center small mb-0">Sign in to your account</p>
      </div>
      <div class="modal-body pt-1 pb-3 px-3">
        <form id="loginForm" autocomplete="off">
          <div class="mb-3">
            <div class="alert alert-danger d-none" id="loginFormErrors"></div>
            <label for="loginEmail" class="form-label mb-1">
              <i class="fas fa-envelope me-1"></i>Email Address
            </label>
            <input type="email" class="form-control rounded-2" id="loginEmail" name="email" placeholder="your@email.com" required>
          </div>
          <div class="mb-3">
            <label for="loginPassword" class="form-label mb-1">
              <i class="fas fa-lock me-1"></i>Password
            </label>
            <input type="password" class="form-control rounded-2" id="loginPassword" name="password" placeholder="Enter password" required>
          </div>
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
              <label class="form-check-label small" for="rememberMe">
                <i class="fas fa-heart me-1"></i>Remember me
              </label>
            </div>
            <a href="#" class="text-decoration-none text-pink small">
              <i class="fas fa-key me-1"></i>Forgot password?
            </a>
          </div>
          <button type="submit" class="btn btn-primary w-100 py-2 rounded-2 fw-bold">
            <i class="fas fa-sign-in-alt me-2"></i>Sign In
          </button>
        </form>
        <div class="text-center mt-2">
          <small>Don't have an account? <a href="#" class="register-link">
            <i class="fas fa-user-plus me-1"></i>Create one
          </a></small>
        </div>
      </div>
    </div>
  </div>
</div>
