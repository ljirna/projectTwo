var EmployeesService = {
  populateTable: function () {
    RestClient.get("employees/performance", function (data) {
      var total = 0;
      data.forEach((element) => {
        total += parseFloat(element.total);
        var noviRed = `
        <tr>
          <td class="text-center">
            <div class="btn-group" role="group">
              <button
                type="button"
                class="btn btn-warning"
                onclick="EmployeesService.edit_employee(${element.id})"
              >
                Edit
              </button>
              <button
                type="button"
                class="btn btn-danger"
                onclick="EmployeesService.delete_employee(${element.id})"
              >
                Delete
              </button>
            </div>
          </td>
          <td>${element.full_name}</td>
          <td>${element.total}</td>
        </tr>`;
        $("#employee-performance tbody").append(noviRed);
      });
      var totalAmountRow = `
          <tr>
          <td></td>
          <td></td>
          <th>Total amount</th>
          </tr>
          <td></td>
          <td></td>
          <td>${total}</td>`;
      $("#employee-performance tbody").append(totalAmountRow);
      console.log(total);
      $("#total").text(total);
    });
  },
};
