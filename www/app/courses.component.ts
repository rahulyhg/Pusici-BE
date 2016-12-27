import {Component} from 'angular2/core'
import {CoursesService} from './courses.service'
import {AutoGrowDirective} from './auto-grow.directive'

@Component({
    selector: 'courses',
    template: `
        <h2>Courses</h2>
        {{ title }}
        <input type="text" autoGrow />
        <ul>
            <li *ngFor="#course of courses">
            {{ course }}
            </li>
        </ul>
        `,
    providers: [CoursesService],
    directives: [AutoGrowDirective]
})
export class CoursesComponent {
    title = "The title of courses page";
    courses;

    // Depedency injection
    // Component is decoupled from service
    constructor(coursesService: CoursesService) {
        this.courses = coursesService.getCourses();
    }
}

/*
1. Create a component
2. Set the properties
3. Use interpolation {{ }}
4. Create a service
5. Use depedency injection
*/
