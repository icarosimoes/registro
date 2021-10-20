

// função exibir imagem ao selecionar - Cadastro de usuários
function enviar_imagem(input) {
if (input.files && input.files[0]) {
   var reader = new FileReader();

      reader.onload = function (e) {
        $('#imgphoto').attr('src', e.target.result);
      }

  reader.readAsDataURL(input.files[0]);
 }
}
$("#photo").change(function(){
enviar_imagem(this);
});