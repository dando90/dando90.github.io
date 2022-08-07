export function setDelay(f, ms) {
    let delay=null;
    return function() {
      if(delay) clearTimeout(delay);
      delay = setTimeout(() => f.apply(this, arguments), ms);
    };
  }