const tat = document.querySelector('.tat')
const taobaiviet = document.querySelector('.create-post')
const nhap = document.querySelector('.input')
const bongmo = document.querySelector('.bongmo')
const likeBtns = document.querySelectorAll('.like-btn')
tat.addEventListener('click', function(){
    taobaiviet.style.display='none'
    bongmo.style.display='none'
    document.body.classList.remove('modal-open');
})
nhap.addEventListener('click', function(){
    taobaiviet.style.display='block'
    bongmo.style.display='block'
    document.body.classList.add('modal-open');
})
likeBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    btn.classList.toggle('liked')
  })
})