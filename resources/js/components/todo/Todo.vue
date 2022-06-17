<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card" v-if="$gate.isAdmin()">
              <div class="card-header">
                <h3 class="card-title">Todo List</h3>

                <div class="card-tools">
                  
                  <button type="button" class="btn btn-sm btn-primary" @click="newModal">
                      <i class="fa fa-plus-square"></i>
                      Add New
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Created</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="todo in todos.data" :key="todo.id">

                      <td>{{todo.id}}</td>
                      <td class="text-capitalize">{{todo.name}}</td>
                      <td>{{todo.created_at}}</td>
                      <td>

                        <a href="#" @click="editModal(todo)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteTodo(todo.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="todos" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>


        <div v-if="!$gate.isAdmin()">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode">Create New Todo</h5>
                    <h5 class="modal-title" v-show="editmode">Update Todo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="editmode ? updateTodo() : createTodo()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input v-model="form.name" type="text" name="name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                        <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
  </section>
</template>

<script>
    export default {
        data () {
            return {
                editmode: false,
                todos : {},
                form: new Form({
                    id : '',
                    name: '',
                })
            }
        },
        methods: {

            getResults(page = 1) {

                  this.$Progress.start();
                  
                  axios.get('/api/todos?page=' + page).then(({ data }) => (this.todos = data.data));

                  this.$Progress.finish();
            },
            updateTodo(){
                this.$Progress.start();
                // console.log('Editing data');
                this.form.put('/api/todos/'+this.form.id)
                .then((response) => {
                    // success
                    $('#addNew').modal('hide');
                    Toast.fire({
                      icon: 'success',
                      title: response.data.message
                    });
                    this.$Progress.finish();
                        //  Fire.$emit('AfterCreate');

                    this.loadTodos();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

            },
            editModal(todo){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(todo);
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            deleteTodo(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {

                        // Send request to the server
                         if (result.value) {
                                this.form.delete('api/todos/'+id).then(()=>{
                                        Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                        );
                                    // Fire.$emit('AfterCreate');
                                    this.loadTodos();
                                }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                         }
                    })
            },

            loadTodos(){
                if(this.$gate.isAdmin()){
                    axios.get("/api/todos").then(({ data }) => (this.todos = data.data));
                }
            },
            
            createTodo(){

                this.form.post('/api/todos')
                .then((response)=>{
                    $('#addNew').modal('hide');

                    Toast.fire({
                            icon: 'success',
                            title: response.data.message
                    });

                    this.$Progress.finish();
                    this.loadTodos();

                })
                .catch(()=>{

                    Toast.fire({
                        icon: 'error',
                        title: 'Some error occured! Please try again'
                    });
                })
            }

        },
        mounted() {
            console.log('Component mounted.')
        },
        created() {

            this.$Progress.start();
            this.loadTodos();
            this.$Progress.finish();
        }
    }
</script>
