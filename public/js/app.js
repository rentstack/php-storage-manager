function generate() {
    document.getElementById('key').value = Math.random().toString(36).slice(2, 10);
}

async function add() {
    const notice = document.getElementById('notice');
    const key = document.getElementById('key');
    const value = document.getElementById('value');
    let formData = new FormData();
    formData.append('key', key.value);
    formData.append('value', value.value);
    notice.innerText = '';
    try {
        const response = await fetch('/api/redis/add', {
            method: 'POST',
            body: formData,
        });
        const result = await response.json();
        if (result['data']['message'] != null) {
            notice.innerText = result['data']['message'];
            key.value = '';
            value.value = '';
            await update();
        } else {
            console.log(result);
        }
    } catch (err) {
        console.log(err);
    }
}

async function update() {
    try {
        let items = document.getElementById('items');
        const response = await fetch('/api/redis', {
            method: 'GET',
        });
        const result = await response.json();
        if (result.data != null) {
            if (Object.keys(result.data).length > 0) {
                let ul = ['<ul class="list-group">','</ul>', ''];
                Object.entries(result.data).forEach(entry => {
                    const [key, value] = entry;
                    ul[2] += `<li class="list-group-item">${key}: ${value} <a href="javascript:void(0)" class="m-lg-5 btn btn-secondary float-end" onclick="remove('${key}')">delete</a></li>`;
                });
                items.innerHTML = ul[0] + ul[2] + ul[1];
            } else {
                items.innerText = 'Items is not found in the Storage.';
            }
        } else {
            console.log(result);
        }
    } catch (err) {
        console.log(err);
    }
}

async function remove(key) {
    try {
        const notice = document.getElementById('notice');
        const response = await fetch('/api/redis/' + key, {
            method: 'DELETE',
        });
        const result = await response.json();
        if (result.code != null) {
            if (result.code === 200) {
                await update();
                notice.innerText = result.data.message;
            } else {
                notice.innerText = result.data.message;
            }
        }
        console.log(result);
    } catch (err) {
        console.log(err);
    }
}

document.addEventListener('DOMContentLoaded', update);