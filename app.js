
// Adiciona um evento de clique nas bolinhas de status
document.addEventListener('DOMContentLoaded', function () {
    const statusCircles = document.querySelectorAll('.status-circle');
    const completedTasksContainer = document.getElementById('completed-tasks-container');
    const toggleCompletedTasksButton = document.getElementById('toggle-completed-tasks');
    const completedTasksList = document.getElementById('completed-tasks-list');

    // Inicializa as tarefas ao carregar a página
    initializeTaskList();

    statusCircles.forEach(circle => {
        circle.addEventListener('click', function () {
            const taskId = this.getAttribute('data-id');
            const taskElement = document.querySelector(`li[data-id='${taskId}']`);
            const taskTitle = taskElement.querySelector('.task-title');
            const taskStatusCircle = taskElement.querySelector('.status-circle');

            // Verifica se a tarefa já está marcada como concluída
            const isCompleted = taskStatusCircle.classList.contains('completed');

            // Atualiza a interface
            if (isCompleted) {
                taskStatusCircle.classList.remove('completed');
                taskTitle.classList.remove('completed');
                // Move a tarefa de volta para a lista de tarefas ativas
                document.getElementById('tasks-list').appendChild(taskElement);
            } else {
                taskStatusCircle.classList.add('completed');
                taskTitle.classList.add('completed');
                // Move a tarefa para a lista de tarefas concluídas
                completedTasksList.appendChild(taskElement);
            }

            // Atualiza o status da tarefa no Google Tasks
            updateTaskStatusInGoogle(taskId, !isCompleted);
        });


        // Eventos para o ícone de editar
        // const editIcons = document.querySelectorAll('.edit-task-icon');
        // editIcons.forEach(icon => {
        //     icon.addEventListener('click', function () {
        //         const taskId = this.getAttribute('data-id');
        //         // Chame a função de edição aqui
        //         openEditTaskModal(taskId);
        //     });
        // });
    });

    // Função para inicializar a lista de tarefas
    function initializeTaskList() {
        const tasks = document.querySelectorAll('.task');
        tasks.forEach(task => {
            const taskId = task.getAttribute('data-id');
            const taskStatusCircle = task.querySelector('.status-circle');
            const taskTitle = task.querySelector('.task-title');

            // Verifica se a tarefa é concluída e move para a lista de tarefas concluídas
            if (taskStatusCircle.classList.contains('completed')) {
                completedTasksList.appendChild(task);
            }
        });
    }

    
    fetch('auth_status.php')
        .then(response => response.json())
        .then(data => {
            const loginBtn = document.getElementById('google-login-btn');

            // Remover event listener anterior caso exista
            loginBtn.removeEventListener('click', handleLoginClick);
            loginBtn.removeEventListener('click', handleLogoutClick);

            if (data.authenticated) {
                const userName = data.user.name || 'Usuário';
                loginBtn.textContent = `Bem-vindo, ${userName} (Logout)`;
                loginBtn.addEventListener('click', handleLogoutClick);
            } else {
                loginBtn.textContent = 'Entrar com o Google';
                loginBtn.addEventListener('click', handleLoginClick);
            }
        })
        .catch(error => console.error('Erro ao verificar autenticação:', error));

    function handleLogoutClick() {
        if (confirm('Tem certeza de que deseja sair?')) {
            window.location.href = 'logout.php';
        }
    }

    function handleLoginClick() {
        window.location.href = 'login.php';
    }


    // Função para minimizar ou expandir a seção de tarefas concluídas
    toggleCompletedTasksButton.addEventListener('click', function () {
        completedTasksList.classList.toggle('hidden');
        toggleCompletedTasksButton.textContent = completedTasksList.classList.contains('hidden') ? 'Tarefas Concluídas (Abrir)' : 'Tarefas Concluídas (Fechar)';
    });
});

// Redireciona para a página de login
// document.getElementById('google-login-btn').addEventListener('click', () => {
//     window.location.href = 'login.php'; // Redireciona para login.php
// });

// Verifica autenticação ao criar tarefa
document.getElementById('create-task-btn').addEventListener('click', () => {
    fetch('auth_status.php') // Checa autenticação no backend
        .then(response => response.json())
        .then(data => {
            if (data.authenticated) {
                document.getElementById('create-task-modal').style.display = 'block'; // Abre modal
                document.getElementById('modal-overlay').style.display = 'block';
            } else {
                alert('Você precisa estar autenticado para criar uma tarefa.');
                window.location.href = 'login.php';
            }
        })
        .catch(error => {
            console.error('Erro na verificação de autenticação:', error);
        });
});

// Fecha o modal de criação de tarefas
document.getElementById('close-create-task-modal').addEventListener('click', () => {
    document.getElementById('create-task-modal').style.display = 'none';
    document.getElementById('modal-overlay').style.display = 'none';
});

// Fecha o modal ao clicar fora dele
document.getElementById('modal-overlay').addEventListener('click', () => {
    document.getElementById('create-task-modal').style.display = 'none';
    document.getElementById('modal-overlay').style.display = 'none';
});



// Exclusão de tarefa
document.querySelectorAll('.delete-task-icon').forEach(button => {
    button.addEventListener('click', (e) => {
        const taskId = e.target.getAttribute('data-id');
        if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
            fetch('delete_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: taskId }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Tarefa excluída com sucesso!');
                        location.reload(); // Recarrega a página
                    } else {
                        alert('Erro ao excluir tarefa');
                    }
                })
                .catch(error => console.error('Erro:', error));
        }
    });
});

// Ao abrir o modal de edição
// document.querySelectorAll(".edit-task-icon").forEach((button) => {
//     button.addEventListener("click", async function () {
//         const taskId = button.getAttribute("data-id");

//         try {
//             const response = await gapi.client.tasks.tasks.get({
//                 tasklist: "@default",
//                 task: taskId,
//             });

//             const task = response.result;

//             document.getElementById("edit-task-title").value = task.title;
//             document.getElementById("edit-task-notes").value = task.notes || "";

//             if (task.due) {
//                 const dueDate = new Date(task.due);
//                 document.getElementById("edit-task-date").value = dueDate.toISOString().split("T")[0];
//                 document.getElementById("edit-task-time").value = dueDate.toTimeString().slice(0, 5);
//             }

//             // Abre o modal de edição
//             document.getElementById("edit-task-modal").style.display = "block";
//         } catch (error) {
//             console.error("Erro ao buscar tarefa:", error);
//         }
//     });
// });




// Lógica para editar tarefa
document.querySelectorAll('.edit-task-icon').forEach(button => {
    button.addEventListener('click', (e) => {
        const taskId = e.target.getAttribute('data-id');

        // Fazendo a requisição para pegar os detalhes da tarefa
        fetch(`get_task_details.php?id=${taskId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Preenchendo o modal de edição com os dados da tarefa
                    document.getElementById('edit-task-id').value = data.task.id;
                    document.getElementById('edit-task-title').value = data.task.title;
                    document.getElementById('edit-task-notes').value = data.task.notes;

                    // Exibindo o modal de edição
                    document.getElementById('edit-task-modal').style.display = 'block';
                    document.getElementById('modal-overlay').style.display = 'block';
                } else {
                    alert('Erro ao carregar os detalhes da tarefa');
                }
            })
            .catch(error => console.error('Erro:', error));
    });
});

// Fechar o modal de edição
document.getElementById('close-edit-task-modal').addEventListener('click', () => {
    document.getElementById('edit-task-modal').style.display = 'none';
    document.getElementById('modal-overlay').style.display = 'none';
});

// Fechar o modal de edição quando clicar no overlay
document.getElementById('modal-overlay').addEventListener('click', () => {
    document.getElementById('edit-task-modal').style.display = 'none';
    document.getElementById('modal-overlay').style.display = 'none';
});

// Enviar as alterações para o servidor ao editar a tarefa
document.getElementById('edit-task-form').addEventListener('submit', (e) => {
    e.preventDefault();

    const taskId = document.getElementById('edit-task-id').value;
    const edittaskTitle = document.getElementById('edit-task-title').value;
    const edittaskNotes = document.getElementById('edit-task-notes').value;
    const edittaskDueDate = document.getElementById('edit-task-due-date');
    const edittaskDueTime = document.getElementById('edit-task-due-time');

    console.log(edittaskTitle, edittaskNotes, edittaskDueDate, edittaskDueTime);

    if (!edittaskTitle) {
        console.error('O Titulo não foi encontrado.');
        return;
    } else if (!edittaskNotes) {
        console.error('A descrição não foi encontrada.');
        return;
    } else if (!edittaskDueDate) {
        console.error('A Data não foi encontrada.');
        return;
    } else if (!edittaskDueTime) {
        console.error('A hora não foi encontrada.');
        return;
    }

    const edittaskTitleValue = edittaskTitle.value;
    const edittaskNotesValue = edittaskNotes.value;
    const edittaskDueDateValue = edittaskDueDate.value;
    const edittaskDueTimeValue = edittaskDueTime.value;


    fetch('update_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: JSON.stringify({
            id: taskId,
            Edit_title: edittaskTitleValue,
            Edit_notes: edittaskNotesValue,
            Edit_due_date: edittaskDueDateValue,
            Edit_due_time: edittaskDueTimeValue
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Resposta do servidor:', data);
        if (data.status === 'success') {
            alert('Tarefa criada com sucesso!');
            // Atualizar UI ou limpar formulário
            location.reload(); // Recarrega a página
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
    });
});






document.getElementById('sort-tasks').addEventListener('change', (e) => {
    const sortBy = e.target.value;
    sortTasks(sortBy);
});

function sortTasks(criteria) {
    // Suponha que você tenha uma lista de tarefas carregada em um array
    const tasks = [...document.querySelectorAll('.task')];

    tasks.sort((a, b) => {
        const aValue = a.querySelector(`.${criteria}`).innerText.toLowerCase();
        const bValue = b.querySelector(`.${criteria}`).innerText.toLowerCase();

        if (aValue < bValue) return -1;
        if (aValue > bValue) return 1;
        return 0;
    });

    // Reorganizar as tarefas na página conforme a ordenação
    const taskList = document.querySelector('.task-list');
    tasks.forEach(task => taskList.appendChild(task));
}





// Notificações de Lembrete
function setTaskReminder(taskId, dueDate) {
    const reminderTime = new Date(dueDate).getTime() - Date.now();

    if (reminderTime > 0) {
        setTimeout(() => {
            alert(`Lembrete: A tarefa ${taskId} está próxima do prazo!`);
        }, reminderTime);
    }
}



// concluir a tarefa
document.querySelectorAll('.complete-task-btn').forEach(button => {
    button.addEventListener('click', (e) => {
        const task = e.target.closest('.task');
        task.classList.toggle('completed');
        updateTaskStatusInDB(task);  // Atualiza o status no banco de dados
    });
});

function updateTaskStatusInDB(task) {
    const taskId = task.getAttribute('data-id');
    const isCompleted = task.classList.contains('completed');

    fetch('update_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: taskId,
            completed: isCompleted
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Status da tarefa atualizado');
        } else {
            console.log('Erro ao atualizar status');
        }
    });
}


// Função para carregar a API do Google Tasks e obter as tarefas
function loadGoogleTasks() {
    gapi.client.load('tasks', 'v1', function() {
        listTasks();
    });
}

// Função para listar as tarefas
function listTasks() {
    gapi.client.tasks.tasks.list({
        tasklist: '@default', // Usa a lista de tarefas padrão
        showCompleted: true, // Exibe as tarefas concluídas
        maxResults: 10 // Limita o número de tarefas retornadas (pode ser ajustado)
    }).then(function(response) {
        const tasks = response.result.items;
        if (tasks && tasks.length > 0) {
            displayTasks(tasks);
        } else {
            console.log("Não há tarefas.");
        }
    });
}

// Função para exibir as tarefas na interface
function displayTasks(tasks) {
    const tasksContainer = document.querySelector('.task-list');
    const completedTasksContainer = document.querySelector('.completed-task-list');

    tasks.forEach(task => {
        const taskElement = document.createElement('div');
        taskElement.classList.add('task');
        taskElement.setAttribute('data-id', task.id);

        // Verificar se a tarefa está concluída
        if (task.status === 'completed') {
            taskElement.classList.add('completed');
            completedTasksContainer.appendChild(taskElement);
        } else {
            tasksContainer.appendChild(taskElement);
        }

        // Exibir título da tarefa
        const taskTitle = document.createElement('span');
        taskTitle.classList.add('task-title');
        taskTitle.textContent = task.title;

        // Exibir botão de concluir
        const completeButton = document.createElement('button');
        completeButton.classList.add('complete-task-btn');
        completeButton.textContent = 'Concluir';
        
        // Adicionar evento para marcar como concluída
        completeButton.addEventListener('click', () => markTaskAsCompleted(task.id, taskElement));

        // Adiciona os elementos ao container da tarefa
        taskElement.appendChild(taskTitle);
        taskElement.appendChild(completeButton);
    });
}

// Função para marcar tarefa como concluída
function markTaskAsCompleted(taskId, taskElement) {
    const request = gapi.client.tasks.tasks.update({
        tasklist: '@default',
        task: taskId,
        status: 'completed' // Marca a tarefa como concluída
    });

    request.then(function(response) {
        taskElement.classList.add('completed');
        moveTaskToCompleted(taskElement);
    });
}

// Função para mover a tarefa concluída para a lista de concluídas
function moveTaskToCompleted(taskElement) {
    const completedTasksContainer = document.querySelector('.completed-task-list');
    completedTasksContainer.appendChild(taskElement);
}




document.getElementById('create-task-form').addEventListener('submit', (e) => {
    e.preventDefault();

    const taskTitle = document.getElementById('task-title');
    const taskNotes = document.getElementById('task-notes');
    const taskDueDate = document.getElementById('task-due-date');
    const taskDueTime = document.getElementById('task-due-time');

    // Verifique se os elementos foram encontrados corretamente
    console.log(taskTitle, taskNotes, taskDueDate, taskDueTime);

    if (!taskTitle) {
        console.error('O Titulo não foi encontrado.');
        return;
    } else if (!taskNotes) {
        console.error('A descrição não foi encontrada.');
        return;
    } else if (!taskDueDate) {
        console.error('A Data não foi encontrada.');
        return;
    } else if (!taskDueTime) {
        console.error('A hora não foi encontrada.');
        return;
    }

    const taskTitleValue = taskTitle.value;
    const taskNotesValue = taskNotes.value;
    const taskDueDateValue = taskDueDate.value;
    const taskDueTimeValue = taskDueTime.value;

    fetch('create_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            title: taskTitleValue,
            notes: taskNotesValue,
            due_date: taskDueDateValue,
            due_time: taskDueTimeValue,
        }),
    })
    .then(response => response.json())
    .then(data => {
        console.log('Resposta do servidor:', data);
        if (data.status === 'success') {
            alert('Tarefa criada com sucesso!');
            // Atualizar UI ou limpar formulário
            location.reload(); // Recarrega a página
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
    });
});


// Função para atualizar o status da tarefa no Google Tasks via backend
function updateTaskStatusInGoogle(taskId, isCompleted) {
    fetch('update_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            taskId: taskId,
            completed: isCompleted
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Status da tarefa atualizado:', data);
    })
    .catch(error => {
        console.error('Erro ao atualizar a tarefa:', error);
    });
}




// Supondo que o ID da tarefa seja passado para esta função
function fetchTaskDetails(taskId) {
    $.ajax({
        url: 'get_task_details.php',  // A URL para o script PHP
        method: 'GET',
        data: { id: taskId },  // Envia o ID da tarefa como parâmetro GET
        success: function(response) {
            try {
                var data = JSON.parse(response); // Certifique-se de analisar o JSON
                if (data.success) {
                    // Se a resposta for bem-sucedida, você pode usar os detalhes da tarefa
                    console.log(data.task);  // Exibe os detalhes da tarefa
                    // Por exemplo, atualize o DOM com os dados da tarefa
                    $('#taskTitle').text(data.task.title);
                    $('#taskNotes').text(data.task.notes);
                } else {
                    console.error('Erro: ' + data.message);  // Caso ocorra um erro na resposta
                }
            } catch (e) {
                console.error('Erro ao processar a resposta: ', e);
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro na requisição AJAX: ', error);
        }
    });
}
