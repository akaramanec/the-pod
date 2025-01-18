const acordions = document.querySelectorAll('.accordion');
// for (let i = 0; i <acordions.length ; i++) {
//
// }

const accordions = document.querySelectorAll('.accordion__content');
for (let i = 0; i < accordions.length; i++) {
  accordions[i].onclick = function() {
    const accordionParent = this.parentElement.parentElement;
    // const accordionParentId = accordionParent.classList[1].split('-')[1];
    const accordionItems = accordionParent.querySelectorAll('details');
    console.log(accordionParent);
    console.log(accordionItems);
    // for (let j = 0; j < accordionItems.length; j++) {
    //   accordionItems[i].classList.add('test');
    //   console.log(accordionItems[i]);
    // }
    // accordionParent.setAttribute('open', `true`);
  };
}


