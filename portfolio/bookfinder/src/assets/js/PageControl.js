export default function PageControl(bookNumber, pageInput)
{
    let bookList = document.querySelector(".bookList");
    let currentPage = pageInput.value;
    let lastPage = Math.ceil(+bookNumber/12);
    let pageElements = [];
    let menuContainer = document.createElement("ul");
    menuContainer.setAttribute("class", "menuContainer");
    bookList.append(menuContainer);

    let index=0;
    if (currentPage>1) {
        pageElements[index] = document.createElement("li");
        pageElements[index].dataset.page = 1;
        pageElements[index].textContent = "First";
        index++;
        
        pageElements[index] = document.createElement("li");
        pageElements[index].dataset.page = (currentPage-1);
        pageElements[index].textContent = "Previous";
        index++;
    }

    pageElements[index] = document.createElement("li");
    if (currentPage<=6) {
        pageElements[index].dataset.page = 1;
        pageElements[index].textContent = "1";
    } else if (currentPage>(lastPage-5)) {
        pageElements[index].dataset.page = (lastPage-10);
        pageElements[index].textContent = (lastPage-10);
    } else {
        pageElements[index].dataset.page = (currentPage-5);
        pageElements[index].textContent = (currentPage-5);
    }
    index++;

    for (let i=index; i<index+10; i++)
    {
        pageElements[i] = document.createElement("li");
        pageElements[i].dataset.page = +pageElements[`${i-1}`].dataset.page+1;
        pageElements[i].textContent = +pageElements[`${i-1}`].dataset.page+1;
    }
    index+=10;

    if (currentPage<lastPage) {
        pageElements[index] = document.createElement("li");
        pageElements[index].dataset.page = +currentPage+1;
        pageElements[index].textContent = "Next";
        index++;

        pageElements[index] = document.createElement("li");
        pageElements[index].dataset.page = lastPage;
        pageElements[index].textContent = "Last";
    }
    
    for (let element of pageElements) {
        if(element.dataset.page==currentPage) {
            element.setAttribute("class", "currentPage");
        }
    }
    menuContainer.append(...pageElements);
    menuContainer.addEventListener("click", handleClick);

    function handleClick (e) {
        if(!e.target.dataset.page) return;
        if(e.target.dataset.page==currentPage) return;
        pageInput.value=e.target.dataset.page;
        menuContainer.remove();
        document.getElementById("bookSearch").click();
        window.scrollTo(0, 0);
    }
}