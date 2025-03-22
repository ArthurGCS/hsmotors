import $ from "jquery"
import bootstrap from "bootstrap"

$(document).ready(() => {
  // Inicializa tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))

  // Confirmação para exclusão
  $(".btn-delete").on("click", (e) => {
    if (!confirm("Tem certeza que deseja excluir este registro?")) {
      e.preventDefault()
    }
  })

  // Máscara para CPF
  $(".cpf-mask").on("input", function () {
    let cpf = $(this).val()
    cpf = cpf.replace(/\D/g, "")
    cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2")
    cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2")
    cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2")
    $(this).val(cpf)
  })

  // Máscara para telefone
  $(".telefone-mask").on("input", function () {
    let telefone = $(this).val()
    telefone = telefone.replace(/\D/g, "")
    telefone = telefone.replace(/^(\d{2})(\d)/g, "($1) $2")
    telefone = telefone.replace(/(\d)(\d{4})$/, "$1-$2")
    $(this).val(telefone)
  })

  // Filtro de tabela
  $("#searchInput").on("keyup", function () {
    var value = $(this).val().toLowerCase()
    $("#dataTable tbody tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    })
  })

  // Animação para cards
  $(".dashboard-card").hover(
    function () {
      $(this).addClass("shadow-lg")
    },
    function () {
      $(this).removeClass("shadow-lg")
    },
  )

  // Auto-fechar alertas após 5 segundos
  setTimeout(() => {
    $(".alert").alert("close")
  }, 5000)
})

