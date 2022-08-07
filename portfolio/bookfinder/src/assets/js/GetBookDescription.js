export default async function GetBookDescription (e)
{
    let key = e.target.parentNode.dataset.key;
    let response = await fetch(`https://openlibrary.org${key}.json`);
    if (response.ok) {
    let json = await response.json();
    let description = (json.description?.value ?? json.description ?? "Description not available for this book");
    showDescription (description);
    } else {
    alert("HTTP-Error: " + response.status);
    }

    function showDescription (description)
    {
        showCover();
        let bookContainer = e.target.parentNode;
        let descriptionElement = document.createElement("div");
        descriptionElement.id = "description-div";
        descriptionElement.innerHTML = `${bookContainer.innerHTML}<p>${description}</p>`;
        let closeButton = document.createElement("img");
        closeButton.setAttribute("class", "closeDescription");
        closeButton.addEventListener("click", closeDescription);
        closeButton.src = "./assets/img/closeImage.svg";
        descriptionElement.append(closeButton);
        bookContainer.append(descriptionElement);
    }

    function closeDescription() {
        document.getElementById("description-div").remove();
        hideCover();
    }

    function showCover() {
      let coverDiv = document.createElement('div');
      coverDiv.id = 'cover-div';
      document.body.style.overflowY = 'hidden';
      document.body.append(coverDiv);
    }

    function hideCover() {
      document.getElementById('cover-div').remove();
      document.body.style.overflowY = '';
    }

}