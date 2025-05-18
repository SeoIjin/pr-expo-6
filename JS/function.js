      //login and register function
      function showLogin() {
        window.location.href = 'login.php';
      }
      function showRegister() {
        window.location.href = 'register.php';
      }
      function closeForm(formId) {
        document.getElementById(formId).style.display = 'none';
      }
      function login() {
        alert("Login successfully!");
        location.reload();
      }
      function register() {
        alert("Account successfully registered!");
        location.reload();
      }
      function toggleMenu () {
        document.getElementById("menu").classList.toggle("active");
      }
      document.getElementById('hamburgerMenu').addEventListener('click', function () {
        this.classList.toggle('active');
        const menu = document.getElementById('menu');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
      });
      // Close the menu when the close button is clicked
      document.getElementById('closeMenu').addEventListener('click', function () {
        const menu = document.getElementById('menu');
        menu.style.display = 'none';
        document.getElementById('hamburgerMenu').classList.remove('active'); // Reset the hamburger icon
      });
      // Add click event to the username to redirect
      document.querySelector('.username').addEventListener('click', function() {
        window.location.href = 'profile.php'; // Change this URL to the actual profile page
      }); 