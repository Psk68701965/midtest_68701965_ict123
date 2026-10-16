<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <router-link class="navbar-brand fw-bold" to="/">68701965midtest</router-link>

      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <router-link class="nav-link" to="/">Home</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/student_crud">Student</router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import { ref } from 'vue'

export default {
  name: 'Navbar',
  setup() {
    const openMenu = ref('')

    const toggleMenu = (menuName) => {
      openMenu.value = openMenu.value === menuName ? '' : menuName
    }

    const closeMenu = () => {
      openMenu.value = ''
    }

    return {
      openMenu,
      toggleMenu,
      closeMenu
    }
  }
}
</script>

<style scoped>
.nav-item.dropdown {
  position: relative;
}

.dropdown-menu {
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  z-index: 1000;
  min-width: 12rem;
  padding: 0.5rem 0;
  margin: 0.125rem 0 0;
  background-color: #fff;
  border: 1px solid rgba(0, 0, 0, 0.15);
  border-radius: 0.25rem;
}

.dropdown-menu.show {
  display: block;
}

.btn-link {
  border: none;
  background: transparent;
  padding: 0.5rem 1rem;
}
</style>


