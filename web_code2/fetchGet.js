fetch('http://172.16.0.5/analog/5/00')
    .then(response => response.json())
    .then(json => document.write(json));