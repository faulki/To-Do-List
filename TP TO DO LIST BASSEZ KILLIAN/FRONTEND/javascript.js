$(document).ready(function() {
  $(document).on("click", "#addTache", function() {
    $.ajax({
      method: "POST",
      url: "http://172.19.0.11/API/index.php?action=tache",
      data: JSON.stringify({/* login ->*/description: $('#description').val(),/*mdp ->*/  categorie: $('#category').val(), priorite: $('#priority').val()})
    })
    .done(function(data, textStatus, jqXHR){
      alert("Réponse reçue avec succès !");
      console.log(data);
      console.log(textStatus);
      console.log(jqXHR);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      alert("Erreur survenue !");
      console.log(jqXHR); 
      console.log(textStatus);
      console.log(errorThrown); 
      });
  });
  function afficher(){
    $.ajax({
      method: "GET",
      url: "http://172.19.0.11/API/index.php?action=tache",
    })
    .done(function(response) {
      console.log(response);
      response.forEach(function(tache) {
      let priorite = '';
      switch (tache.priorite_tache) {
        case "1":
          priorite = "Très important";
          break;
        case "2":
          priorite = "Important";
          break;
        case "3":
          priorite = "Peu important";
          break;
        default:
          priorite = "";
          break;
      }
        $('#task-list').append(
          '<tr>' +
          '<td>' + tache.desc_tache + '</td>' +
          '<td>' + tache.categorie_tache + '</td>' +
          '<td id="numPriority">' + priorite + '</td>' +
          '<td><button class="supprimer-tache" data-id="' + tache.id_tache + '">Supprimer</button></td>' +
          '<td><button class="modifier-tache" data-id="' + tache.id_tache + '">Modifier</button></td>' +
          '</tr>'
        );
      });
    });
}

afficher();

  $(document).on("click", ".supprimer-tache", function() {
  var id_tache = $(this).data('id');
  $.ajax({
    method: "DELETE",
    url: "http://172.19.0.11/API/index.php?action=tache",
    data: JSON.stringify({id: id_tache})
  })
  .done(function(response) {
    alert("La tâche a été supprimée avec succès !");
    $('#task-list').empty(); 
    afficher();
  })
  .fail(function(jqXHR, textStatus, errorThrown) {
    alert("Erreur lors de la suppression de la tâche !");
    console.log(jqXHR); 
    console.log(textStatus); 
    console.log(errorThrown); 
  });
});

$(document).on("click", ".modifier-tache", function() {
  var id_tache = $(this).data('id');
  
  var description = $(this).closest('tr').find('td:eq(0)').text();
  var categorie = $(this).closest('tr').find('td:eq(1)').text();
  var priorite = $(this).closest('tr').find('td:eq(2)').text();
  
  $(this).closest('tr').find('td:eq(0)').html('<input type="text" class="form-control" id="edit-description" value="' + description + '">');
  $(this).closest('tr').find('td:eq(1)').html('<input type="text" class="form-control" id="edit-category" value="' + categorie + '">');
  $(this).closest('tr').find('td:eq(2)').html('<select class="form-control" id="prioritemod"><option value="1" ' + (priorite == "Très important" ? "selected" : "") + '>Très important</option><option value="2" ' + (priorite == "Important" ? "selected" : "") + '>Important</option><option value="3" ' + (priorite == "Peu important" ? "selected" : "") + '>Peu important</option></select>');
  
  $(this).closest('tr').find('td:eq(3)').html('<button class="confirmer-modification btn btn-success" style="background-color: green;" data-id="' + id_tache + '">Confirmer</button>');
  $(this).closest('tr').find('td:eq(4)').html('<button class="annuler-modification btn btn-danger">Annuler</button>');
});

$(document).on("click", ".annuler-modification", function() {
  $('#task-list').empty();
  afficher();
});

$(document).on("click", ".confirmer-modification", function() {
  var id_tache = $(this).data('id');
  
  var description = $('#edit-description').val();
  var categorie = $('#edit-category').val();
  var priorite = $('#prioritemod').val();

  console.log(".confirmer-modification");
  
  $.ajax({
    method: "PUT",
    url: "http://172.19.0.11/API/index.php?action=tache",
    data: JSON.stringify({id: id_tache, description: description, categorie: categorie, priorite: priorite})
  })
  .done(function(response) {
    alert("La tâche a été modifiée avec succès !");
    $('#task-list').empty();
    afficher();
  })
  .fail(function(jqXHR, textStatus, errorThrown) {
    alert("Erreur lors de la modification de la tâche !");
    console.log(jqXHR);
    console.log(textStatus); 
    console.log(errorThrown); 
  });
});

function disableEditButtons(except) {
  $('.modifier-tache').not(except).prop('disabled', true);
}

function enableEditButtons() {
  $('.modifier-tache').prop('disabled', false);
}


$(document).on('click', '.modifier-tache', function() {
  disableEditButtons(this);

  $(this).closest('tr').addClass('edit-mode');
});

$(document).on('click', '.confirmer-modification', function() {
  enableEditButtons();

  $(this).closest('tr').removeClass('edit-mode');
});

$(document).on('click', '.annuler-modification', function() {
  enableEditButtons();

  $(this).closest('tr').removeClass('edit-mode');

  $(this).siblings('.edit-form').find('.edit-description').val($(this).siblings('.task-name').text());
  $(this).siblings('.edit-form').find('.edit-category').val($(this).siblings('.category').text());
  $(this).siblings('.edit-form').find('.edit-priority').val($(this).siblings('.priority').data('value'));
});

function getPriorityLabel(priorityCode) {
  switch (priorityCode) {
    case "1":
      return "Très important";
    case "2":
      return "Important";
    case "3":
      return "Peu important";
    default:
      return "";
  }
}

// Obtenez toutes les cellules de priorité dans le tableau
const priorityCells = document.querySelectorAll("#task-list td:nth-child(3)");

// Parcourez chaque cellule de priorité et remplacez le contenu numérique par le libellé correspondant
priorityCells.forEach((cell) => {
  const priorityCode = cell.textContent.trim();
  const priorityLabel = getPriorityLabel(priorityCode);
  cell.textContent = priorityLabel;
});

const taskNameInput = document.getElementById("description");
const taskNameCounter = document.getElementById("task-name-counter");

taskNameInput.addEventListener("input", function() {
  const remainingChars = taskNameInput.maxLength - taskNameInput.value.length;
  taskNameCounter.textContent = remainingChars;
});

$('#mode-nuit').click(function() {
    $('body').toggleClass('mode-nuit');
  });

});