@extends('layouts.admin')

@section('title', 'Create User Notifications')
@section('content-header', 'Create User Notifications')
@section('content-actions')
    <a href="{{route('notifications.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To User Notifications</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('notifications.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="user_id">User ID</label>
                    <input type="number" name="user_id" class="form-control @error('user_id') is-invalid @enderror"
                           id="user_id"
                           placeholder="User ID" value="{{ old('user_id') }}">
                    @error('user_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <button type="button" class="btn btn-primary" onclick="toggleUserListModal()">Select User</button>

                <div class="form-group"><br>
                    <label for="notify_user_id">Notify User ID</label>
                    <input type="number" name="notify_user_id" class="form-control @error('notify_user_id') is-invalid @enderror"
                           id="notify_user_id"
                           placeholder="Notify User ID" value="{{ old('notify_user_id') }}">
                    @error('notify_user_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <button type="button" class="btn btn-primary" onclick="toggleNotifyUserListModal()">Select Notify User</button>

                 <div class="form-group"><br>
                    <label for="message">Message</label>
                    <textarea name="message" class="form-control @error('message') is-invalid @enderror" id="message" rows="3" placeholder="Message">{{ old('message') }}</textarea>
                    @error('message')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                <button class="btn btn-success btn-block btn-lg" type="submit">Submit</button>
            </form>
        </div>
    </div>

    <!-- User List Modal -->
    <div id="userListModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="toggleUserListModal()">&times;</span>
            <h2>User List</h2>
            <!-- Search input -->
            <input type="text" id="searchInput" oninput="searchUsers()" placeholder="Search...">
            <div class="table-responsive">
                <table class="table table-bordered" id="userTable">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="selected_user_id" value="{{ $user->id }}" onclick="selectUser(this)">
                                </div>
                            </td>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->mobile }}</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <nav aria-label="User List Pagination">
                <ul class="pagination justify-content-center">
                    <!-- Previous button -->
                    <li class="page-item">
                        <button class="page-link" onclick="prevPage()" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </button>
                    </li>

                    <!-- Next button -->
                    <li class="page-item">
                        <button class="page-link" onclick="nextPage()" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Notify User List Modal -->
    <div id="notifyUserListModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="toggleNotifyUserListModal()">&times;</span>
            <h2>Notify User List</h2>
            <!-- Search input -->
            <input type="text" id="notifySearchInput" oninput="searchNotifyUsers()" placeholder="Search...">
            <div class="table-responsive">
                <table class="table table-bordered" id="notifyUserTable">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="selected_notify_user_id" value="{{ $user->id }}" onclick="selectNotifyUser(this)">
                                </div>
                            </td>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->mobile }}</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <nav aria-label="Notify User List Pagination">
                <ul class="pagination justify-content-center">
                    <!-- Previous button -->
                    <li class="                page-item">
                        <button class="page-link" onclick="prevNotifyPage()" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </button>
                    </li>

                    <!-- Next button -->
                    <li class="page-item">
                        <button class="page-link" onclick="nextNotifyPage()" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

@endsection
@section('js')
    <!-- Include any additional JavaScript if needed -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Define variables for pagination
        var currentPage = 1;
        var notifyCurrentPage = 1;
        var itemsPerPage = 10; // Change this value as needed
        var userListRows = $('#userTable tbody tr');
        var notifyUserListRows = $('#notifyUserTable tbody tr');

        // Function to toggle the user list modal
        function toggleUserListModal() {
            $('#userListModal').toggle(); // Toggle the modal
        }

        // Function to toggle the notify user list modal
        function toggleNotifyUserListModal() {
            $('#notifyUserListModal').toggle(); // Toggle the modal
        }

        // Function to filter user list based on search input
        function searchUsers() {
            var searchText = $('#searchInput').val().toLowerCase();
            userListRows.each(function() {
                var id = $(this).find('td:eq(1)').text().toLowerCase();
                var name = $(this).find('td:eq(2)').text().toLowerCase();
                var mobile = $(this).find('td:eq(3)').text().toLowerCase();
                var email = $(this).find('td:eq(4)').text().toLowerCase();
                if (id.includes(searchText) || name.includes(searchText) || mobile.includes(searchText) || email.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Function to filter notify user list based on search input
        function searchNotifyUsers() {
            var notifySearchText = $('#notifySearchInput').val().toLowerCase();
            notifyUserListRows.each(function() {
                var id = $(this).find('td:eq(1)').text().toLowerCase();
                var name = $(this).find('td:eq(2)').text().toLowerCase();
                var mobile = $(this).find('td:eq(3)').text().toLowerCase();
                var email = $(this).find('td:eq(4)').text().toLowerCase();
                if (id.includes(notifySearchText) || name.includes(notifySearchText) || mobile.includes(notifySearchText) || email.includes(notifySearchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Function to handle checkbox click and update user_id input
        function selectUser(checkbox) {
            $('input[name="selected_user_id"]').prop('checked', false); // Deselect all checkboxes
            $(checkbox).prop('checked', true); // Select only the clicked checkbox
            $('#user_id').val(checkbox.value); // Set its value to the user_id input field
            toggleUserListModal(); // Close the modal
        }

        // Function to handle checkbox click and update notify_user_id input
        function selectNotifyUser(checkbox) {
            $('input[name="selected_notify_user_id"]').prop('checked', false); // Deselect all checkboxes
            $(checkbox).prop('checked', true); // Select only the clicked checkbox
            $('#notify_user_id').val(checkbox.value); // Set its value to the notify_user_id input field
            toggleNotifyUserListModal(); // Close the modal
        }

        // Function to show the specified page of users
        function showPage(page) {
            var startIndex = (page - 1) * itemsPerPage;
            var endIndex = startIndex + itemsPerPage;
            userListRows.hide().slice(startIndex, endIndex).show();
        }

        // Function to show the specified page of notify users
        function showNotifyPage(notifyPage) {
            var startIndex = (notifyPage - 1) * itemsPerPage;
            var endIndex = startIndex + itemsPerPage;
            notifyUserListRows.hide().slice(startIndex, endIndex).show();
        }

        // Function to go to the previous page
        function prevPage() {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        }

        // Function to go to the next page
        function nextPage() {
            if (currentPage < Math.ceil(userListRows.length / itemsPerPage)) {
                currentPage++;
                showPage(currentPage);
            }
        }

        // Function to go to the previous notify page
        function prevNotifyPage() {
            if (notifyCurrentPage > 1) {
                notifyCurrentPage--;
                showNotifyPage(notifyCurrentPage);
            }
        }

        // Function to go to the next notify page
        function nextNotifyPage() {
            if (notifyCurrentPage < Math.ceil(notifyUserListRows.length / itemsPerPage)) {
                notifyCurrentPage++;
                showNotifyPage(notifyCurrentPage);
            }
        }

        // Show the first page initially
        showPage(currentPage);
        showNotifyPage(notifyCurrentPage);
    </script>
@endsection

