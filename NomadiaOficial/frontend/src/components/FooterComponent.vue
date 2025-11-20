<template>
  <div>
    <footer class="footer mt-5 py-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
      <div class="container">
        <div class="row g-4">
          <div class="col-md-4">
            <div class="d-flex align-items-center mb-3">
              <div class="brand-mark me-2">N</div>
              <h5 class="text-white mb-0">NOMADIA</h5>
            </div>
            <p class="text-white-50">Conecta con guías locales y vive experiencias auténticas.</p>
            <div class="social-links">
              <a href="https://facebook.com/nomadia" target="_blank" rel="noopener noreferrer" class="text-white me-3" title="Facebook">
                <i class="bi bi-facebook fs-5"></i>
              </a>
              <a href="https://instagram.com/nomadia" target="_blank" rel="noopener noreferrer" class="text-white me-3" title="Instagram">
                <i class="bi bi-instagram fs-5"></i>
              </a>
              <a href="https://twitter.com/nomadia" target="_blank" rel="noopener noreferrer" class="text-white me-3" title="Twitter">
                <i class="bi bi-twitter fs-5"></i>
              </a>
              <a href="https://youtube.com/@nomadia" target="_blank" rel="noopener noreferrer" class="text-white" title="YouTube">
                <i class="bi bi-youtube fs-5"></i>
              </a>
            </div>
          </div>

          <div class="col-md-2">
            <h6 class="text-white fw-bold mb-3">Explorar</h6>
            <ul class="list-unstyled">
              <li class="mb-2">
                <router-link to="/" class="text-white-50 text-decoration-none hover-link">Inicio</router-link>
              </li>
              <li class="mb-2">
                <a @click.prevent="showModal('como-funciona')" href="#" class="text-white-50 text-decoration-none hover-link">Cómo Funciona</a>
              </li>
              <li class="mb-2">
                <a @click.prevent="showModal('sobre-nosotros')" href="#" class="text-white-50 text-decoration-none hover-link">Sobre Nosotros</a>
              </li>
            </ul>
          </div>

          <div class="col-md-3">
            <h6 class="text-white fw-bold mb-3">Para Guías</h6>
            <ul class="list-unstyled">
              <li class="mb-2">
                <router-link to="/guia" class="text-white-50 text-decoration-none hover-link">Panel de Guía</router-link>
              </li>
              <li class="mb-2">
                <a @click.prevent="showModal('ser-guia')" href="#" class="text-white-50 text-decoration-none hover-link">Cómo ser Guía</a>
              </li>
              <li class="mb-2">
                <a @click.prevent="showModal('ayuda')" href="#" class="text-white-50 text-decoration-none hover-link">Centro de Ayuda</a>
              </li>
            </ul>
          </div>

          <div class="col-md-3">
            <h6 class="text-white fw-bold mb-3">Legal</h6>
            <ul class="list-unstyled">
              <li class="mb-2">
                <a @click.prevent="showModal('terminos')" href="#" class="text-white-50 text-decoration-none hover-link">Términos</a>
              </li>
              <li class="mb-2">
                <a @click.prevent="showModal('privacidad')" href="#" class="text-white-50 text-decoration-none hover-link">Privacidad</a>
              </li>
              <li class="mb-2">
                <a @click.prevent="showModal('contacto')" href="#" class="text-white-50 text-decoration-none hover-link">Contacto</a>
              </li>
            </ul>
          </div>
        </div>

        <hr class="border-secondary my-4" />

        <div class="row">
          <div class="col-md-6 text-center text-md-start">
            <p class="text-white-50 mb-0">© 2025 Nomadia. Todos los derechos reservados.</p>
          </div>
          <div class="col-md-6 text-center text-md-end">
            <p class="text-white-50 mb-0">Hecho con <span class="text-danger">♥</span> para viajeros</p>
          </div>
        </div>
      </div>
    </footer>

    <div class="modal fade" id="footerModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">{{ modalTitle }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div v-html="modalContent"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Modal } from 'bootstrap'

const modalTitle = ref('')
const modalContent = ref('')

const contentMap = {
  'como-funciona': { title: 'Cómo Funciona', content: '<p>Conectamos a viajeros con guías locales en Chuquisaca para vivir experiencias auténticas, gestionando reservas y pagos de forma segura.</p>' },
  'sobre-nosotros': { title: 'Sobre Nosotros', content: '<p>Somos una plataforma dedicada a impulsar el turismo sostenible, valorando la cultura local y ofreciendo aventuras únicas en Bolivia.</p>' },
  'ser-guia': { title: 'Ser Guía', content: '<p>Comparte tu pasión y cultura. Regístrate gratis, publica tus experiencias, gestiona tu calendario y genera ingresos extras.</p>' },
  'ayuda': { title: 'Ayuda', content: '<p>¿Tienes dudas? Encuentra respuestas sobre cómo reservar, gestionar tu cuenta o resolver problemas técnicos.</p>' },
  'terminos': { title: 'Términos', content: '<p>Conoce las reglas de uso de la plataforma, nuestras políticas de cancelación y las normas de la comunidad.</p>' },
  'privacidad': { title: 'Privacidad', content: '<p>Tu seguridad es prioridad. Consulta cómo protegemos tus datos personales y la información de tus pagos encriptados.</p>' },
  'contacto': { title: 'Contacto', content: '<p>Para soporte técnico o consultas generales: 📧 soporte@nomadia.com 📍 Sucre, Chuquisaca, Bolivia.</p>' }
}

const showModal = (type) => {
  const content = contentMap[type]
  if (content) {
    modalTitle.value = content.title
    modalContent.value = content.content
    const modalElement = document.getElementById('footerModal')
    const modal = new Modal(modalElement)
    modal.show()
  }
}
</script>

<style scoped>
.footer { margin-top: auto; }
.brand-mark {
  width: 36px; height: 36px; border-radius: 8px;
  background: #e63946; display: flex;
  align-items: center; justify-content: center;
  color: #fff; font-weight: 700; font-size: 18px;
}
.hover-link { transition: all 0.2s ease; }
.hover-link:hover { color: #fff !important; transform: translateX(4px); }
.social-links a { transition: all 0.2s ease; }
.social-links a:hover { transform: translateY(-3px); opacity: 0.8; }
</style>
