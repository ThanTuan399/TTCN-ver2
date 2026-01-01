const clb = document.querySelector('.clb')
const sk = document.querySelector('.sk')
const user = document.querySelector('.user')
const qlyclb = document.querySelector('.qlyclb')
const qlysukien = document.querySelector('.qlysukien')
const qlyuser = document.querySelector('.qlyuser')
const bacham = document.querySelectorAll('.three-dot')
const delet = document.querySelectorAll('.delete')
const tatdelet = document.querySelector('.right-bottom-content')
// tatdelet.addEventListener('click', function(){
//     delet.style.display='none'
// })
clb.addEventListener('click', function(){
    clb.classList.add('dangclick')
    sk.classList.remove('dangclick')
    user.classList.remove('dangclick')
    qlysukien.classList.add('hidden')
    qlyclb.classList.remove('hidden')
    qlyuser.classList.add('hidden')
})
sk.addEventListener('click', function(){
    clb.classList.remove('dangclick')
    sk.classList.add('dangclick')
    user.classList.remove('dangclick')
    qlysukien.classList.remove('hidden')
    qlyclb.classList.add('hidden')
    qlyuser.classList.add('hidden')
})
user.addEventListener('click', function(){
    clb.classList.remove('dangclick')
    sk.classList.remove('dangclick')
    user.classList.add('dangclick')
    qlysukien.classList.add('hidden')
    qlyclb.classList.add('hidden')
    qlyuser.classList.remove('hidden')
})

bacham.forEach(btn => {
  btn.addEventListener('click', function (e) {
    e.stopPropagation()
    const post = btn.parentElement
    const menu = post.querySelector('.delete')
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
