export default async function ImgAutoLoad()
{
    let isAllLoaded = false
    let allImages = document.querySelectorAll('.bookContainer img');
    let toLoad = allImages.length;
    for (let img of allImages) {
        let id = img.dataset.id;
        if (!id) {
         toLoad--;
        continue;
        }
        if (isVisible(img)) {
            img.src = await getImage(id);
            img.dataset.id = "";
        }
    }


    function isVisible(elem) {
        let coords = elem.getBoundingClientRect();
        let windowHeight = document.documentElement.clientHeight;
        let topVisible = coords.top > 0 && coords.top < windowHeight;
        let bottomVisible = coords.bottom < windowHeight && coords.bottom > 0;
        return topVisible || bottomVisible;
    }

    async function getImage(coverId) {
        let response = await fetch(`https://covers.openlibrary.org/b/id/${coverId}-M.jpg`);
        if (response.ok) {
        let imageBlob = await response.blob();
        return URL.createObjectURL(imageBlob);
        } else {
        alert("HTTP-Error: " + response.status);
        }
    }

    if (!toLoad) window.removeEventListener("scroll", ImgAutoLoad);
}