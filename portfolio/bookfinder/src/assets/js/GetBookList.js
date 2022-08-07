import FetchBooks from "./FetchBooks.js";
import ImgAutoLoad from "./ImgAutoLoad.js";
import GetBookDescription from "./GetBookDescription.js";
import PageControl from "./PageControl.js";

export default async function GetBookList (bookForm, bookContainer)
{
  let a = null;
  let subjectInput = bookForm.elements.subject
  let forPage = 12;
  let pageInput = bookForm.elements.page;
  let lastSubject = subjectInput.value;

  bookForm.addEventListener("submit", showList);

  async function showList (e)
  {
    e.preventDefault();
    let subject = subjectInput.value
    if (!subject.trim()) return false;
    if(subject!=lastSubject) {
      bookForm.elements.page.value=1;
      lastSubject = subject;
    }
    let page = bookForm.elements.page.value;
    let offset = (+page-1)*forPage;
    let b = null;
    closeList();
    
    a = document.createElement("div");
    a.setAttribute("class", "bookList");
    bookContainer.append(a);
    let json = await FetchBooks(subject, forPage, offset);
    let resultNumber = json.work_count;
    let arr = json.works;
    
    if (!resultNumber) {
      a.textContent = "No books found"
      return false;
    }

    let subjectHeader = document.createElement("div");
    subjectHeader.setAttribute("class", "subjectHeader");
    let title = document.createElement("h2");
    title.setAttribute("class", "subjectTitle");
    title.textContent = json.name.toUpperCase();
    subjectHeader.append(title);
    let workCount = document.createElement("h4");
    workCount.setAttribute("class", "workCount");
    workCount.textContent = `${json.work_count} books`;
    subjectHeader.append(workCount);
    a.append(subjectHeader);

    for (let i = 0; i < arr.length; i++) {
        b = document.createElement("div");
        b.setAttribute("class", "bookItem");
        b.dataset.key = arr[i].key;

        let title = document.createElement("h2");
        title.setAttribute("class", "bookTitle");
        title.textContent = arr[i].title;
        b.append(title);
        title.addEventListener("click", GetBookDescription);

        let authors = document.createElement("h4");
        arr[i].allAuthors = arr[i].authors.map(author => author.name).join(", ");
        authors.textContent = arr[i].allAuthors;
        b.append(authors);
        
        let bookImage = document.createElement("img");
        bookImage.setAttribute("class", "bookImage");
        arr[i].src = arr[i].cover_id ? "./assets/img/placeholder.svg" : "./assets/img/noimage.svg"
        arr[i].id = arr[i].cover_id ? arr[i].cover_id : ""
        bookImage.src = arr[i].src
        bookImage.dataset.id = arr[i].id
        bookImage.width = 180
        b.append(bookImage);
        
        a.append(b);
    }

    PageControl(resultNumber, pageInput);
    ImgAutoLoad();
    window.addEventListener("scroll", ImgAutoLoad);
    
    function closeList() {
    if (a) a.remove();
    a=null;
    }
  }

}