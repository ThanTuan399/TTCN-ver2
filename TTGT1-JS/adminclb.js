const delet = document.querySelectorAll('.delet')
const bacham = document.querySelectorAll('.bacham')
const user = document.querySelector('.user')
const post = document.querySelector('.post')
const nonepost = document.querySelector('.nonepost')
const qlyuser = document.querySelector('.qlyuser')
const qlypost = document.querySelector('.qlypost')
const qlynonepost = document.querySelector('.qlynonepost')
const likeBtns = document.querySelectorAll('.like-btn')
const bars = document.querySelector('.bars')
const bongmo = document.querySelector('.bongmo')
const tat = document.querySelector('.tat')
const nhap = document.querySelector('.doimk')
user.addEventListener('click', function(){
    // post.classList.add('.hidden')
    // nonepost.classList.add('.hidden')
    user.classList.add('dangclick')
    post.classList.remove('dangclick')
    nonepost.classList.remove('dangclick')
    qlyuser.classList.remove('hidden')
    qlypost.classList.add('hidden')
    qlynonepost.classList.add('hidden')
})
post.addEventListener('click', function(){
    user.classList.remove('dangclick')
    post.classList.add('dangclick')
    nonepost.classList.remove('dangclick')
    qlyuser.classList.add('hidden')
    qlypost.classList.remove('hidden')
    qlynonepost.classList.add('hidden')
})
nonepost.addEventListener('click', function(){
    user.classList.remove('dangclick')
    post.classList.remove('dangclick')
    nonepost.classList.add('dangclick')
    qlyuser.classList.add('hidden')
    qlypost.classList.add('hidden')
    qlynonepost.classList.remove('hidden')
})

bacham.forEach(btn => {
  btn.addEventListener('click', function (e) {
    e.stopPropagation()
    const post = btn.parentElement
    const menu = post.querySelector('.delet')
    menu.classList.remove('hidden')
  })
})
delet.forEach(menu => {
  menu.addEventListener('click', function (e) {
    e.stopPropagation()
  })
})
document.addEventListener('click', function () {
  delet.forEach(menu => {
    menu.classList.add('hidden')
  })
})

likeBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    btn.classList.toggle('liked')
  })
})

tat.addEventListener('click', function(){
    bars.style.display='none'
    bongmo.style.display='none'
    document.body.classList.remove('modal-open');
})
nhap.addEventListener('click', function(){
    bars.style.display='block'
    bongmo.style.display='block'
    document.body.classList.add('modal-open');
})