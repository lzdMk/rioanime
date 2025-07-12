<!-- Registration Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-light rounded-4 border-0 shadow-lg">
      <button type="button" class="btn-close btn-close-white position-absolute end-0 mt-3 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-header border-0 flex-column align-items-center pt-5 pb-0">
        <h2 class="modal-title w-100 text-center fw-bold mb-2" id="registerModalLabel">Create an Account</h2>
      </div>
      <div class="modal-body pt-2 pb-4 px-4">
        <form id="registerForm" autocomplete="off">
          <div class="mb-3">
            <label for="registerName" class="form-label text-uppercase small">Your Name</label>
            <input type="text" class="form-control form-control-lg rounded-3 bg-dark text-light border-secondary" id="registerName" name="name" placeholder="Name" required>
          </div>
          <div class="mb-3">
            <label for="registerEmail" class="form-label text-uppercase small">Email Address</label>
            <input type="email" class="form-control form-control-lg rounded-3 bg-dark text-light border-secondary" id="registerEmail" name="email" placeholder="name@email.com" required>
          </div>
          <div class="mb-3">
            <label for="registerPassword" class="form-label text-uppercase small">Password</label>
            <input type="password" class="form-control form-control-lg rounded-3 bg-dark text-light border-secondary" id="registerPassword" name="password" placeholder="Password" required>
          </div>
          <div class="mb-3">
            <label for="registerConfirmPassword" class="form-label text-uppercase small">Confirm Password</label>
            <input type="password" class="form-control form-control-lg rounded-3 bg-dark text-light border-secondary" id="registerConfirmPassword" name="confirm_password" placeholder="Confirm Password" required>
          </div>
          <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold">Register</button>
        </form>
        <div class="text-center mt-3">
          Have an account? <a href="#" class="login-link">Login</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-light rounded-4 border-0 shadow-lg">
      <button type="button" class="btn-close btn-close-white position-absolute end-0 mt-3 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-header border-0 flex-column align-items-center pt-5 pb-0">
        <h2 class="modal-title w-100 text-center fw-bold mb-2" id="loginModalLabel">Welcome back!</h2>
      </div>
      <div class="modal-body pt-2 pb-4 px-4">
        <form id="loginForm" autocomplete="off">
          <div class="mb-3">
            <label for="loginEmail" class="form-label text-uppercase small">Email Address</label>
            <input type="email" class="form-control form-control-lg rounded-3 bg-dark text-light border-secondary" id="loginEmail" name="email" placeholder="name@email.com" required>
          </div>
          <div class="mb-3">
            <label for="loginPassword" class="form-label text-uppercase small">Password</label>
            <input type="password" class="form-control form-control-lg rounded-3 bg-dark text-light border-secondary" id="loginPassword" name="password" placeholder="Password" required>
          </div>
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
              <label class="form-check-label small" for="rememberMe">Remember me</label>
            </div>
            <a href="#" class="text-decoration-none text-pink small">Forgot password?</a>
          </div>
          <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold">Login</button>
        </form>
        <div class="text-center mt-3">
          Don’t have an account? <a href="#" class="register-link">Register</a>
        </div>
      </div>
    </div>
  </div>
</div>