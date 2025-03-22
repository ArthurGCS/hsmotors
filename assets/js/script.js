$(document).ready(() => {
  // Navbar scroll effect
  $(window).scroll(function () {
    if ($(this).scrollTop() > 50) {
      $(".navbar").addClass("scrolled")
    } else {
      $(".navbar").removeClass("scrolled")
    }
  })

  // Smooth scrolling for anchor links
  $('a[href^="#"]').on("click", function (e) {
    e.preventDefault()

    var target = this.hash
    var $target = $(target)

    if ($target.length) {
      $("html, body").animate(
        {
          scrollTop: $target.offset().top - 70,
        },
        800,
        "swing",
      )
    }
  })

  // Filtro de veículos por marca
  $("#btnFiltrar").on("click", () => {
    var marca = $("#filtroMarca").val()

    if (marca === "") {
      // Mostrar todos os veículos
      $(".veiculo-item").show()
    } else {
      // Filtrar por marca
      $(".veiculo-item").hide()
      $('.veiculo-item[data-marca="' + marca + '"]').show()
    }
  })

  // Formulário de contato
  $("#formContato").on("submit", function (e) {
    e.preventDefault()

    // Mostrar loading
    const submitBtn = $(this).find('button[type="submit"]')
    const originalText = submitBtn.html()
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Enviando...')
    submitBtn.prop("disabled", true)

    // Simular envio com timeout
    setTimeout(() => {
      // Mostrar mensagem de sucesso
      const formContainer = $(this).parent()
      const successMessage = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fas fa-check-circle me-2"></i> Mensagem enviada com sucesso! Em breve entraremos em contato.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      `
      formContainer.prepend(successMessage)

      // Resetar formulário
      this.reset()

      // Restaurar botão
      submitBtn.html(originalText)
      submitBtn.prop("disabled", false)

      // Esconder alerta após 5 segundos
      setTimeout(() => {
        $(".alert").alert("close")
      }, 5000)
    }, 1500)
  })

  // Formulário de interesse no veículo
  $("#formVeiculoInteresse").on("submit", function (e) {
    e.preventDefault()

    // Mostrar loading
    const submitBtn = $(this).find('button[type="submit"]')
    const originalText = submitBtn.html()
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Enviando...')
    submitBtn.prop("disabled", true)

    // Simular envio com timeout
    setTimeout(() => {
      // Mostrar mensagem de sucesso
      const formContainer = $(this).parent()
      const successMessage = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fas fa-check-circle me-2"></i> Sua mensagem de interesse foi enviada com sucesso! Em breve entraremos em contato.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      `
      formContainer.prepend(successMessage)

      // Resetar formulário
      this.reset()

      // Restaurar botão
      submitBtn.html(originalText)
      submitBtn.prop("disabled", false)

      // Esconder alerta após 5 segundos
      setTimeout(() => {
        $(".alert").alert("close")
      }, 5000)
    }, 1500)
  })

  // Máscara para telefone
  $(".telefone-mask").on("input", function () {
    let telefone = $(this).val()
    telefone = telefone.replace(/\D/g, "")
    telefone = telefone.replace(/^(\d{2})(\d)/g, "($1) $2")
    telefone = telefone.replace(/(\d)(\d{4})$/, "$1-$2")
    $(this).val(telefone)
  })

  // Inicializa tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))

  // Animação para elementos ao rolar a página
  function animateOnScroll() {
    $(".animate-on-scroll").each(function () {
      const elementTop = $(this).offset().top
      const elementHeight = $(this).outerHeight()
      const windowHeight = $(window).height()
      const scrollY = $(window).scrollTop()

      if (scrollY > elementTop - windowHeight + elementHeight / 2) {
        $(this).addClass("animated")
      }
    })
  }

  // Inicializar animações
  $(window).on("scroll", animateOnScroll)
  animateOnScroll() // Executar uma vez no carregamento

  // Fechar menu mobile ao clicar em um item
  $(".navbar-nav>li>a").on("click", () => {
    $(".navbar-collapse").collapse("hide")
  })
})

