const person = {
  firstName: "John",
  lastName: "Doe",
  id: 5566,
  fullName: function () {
    return this.firstName + " " + this.lastName;
  },
};
console.log(person.fullName());

const myCar = {
  make: "Maruthi-800",
  year: "2012",
  color: "red",
};
