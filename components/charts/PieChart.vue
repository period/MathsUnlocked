<script>
import { Pie } from 'vue-chartjs'

export default {
    name: "PieChart",
    extends: Pie,
    props: {
        data: {
            type: Object
        }
    },
    mounted () {
        let colours = ["#C0C0C0", "#FF0000", "#FFFF00", "#00FF00", "#00FFFF", "#0000FF", "#FF00FF"]
        for(var i = 0; i < this.data.datasets.length; i++) this.data.datasets[i].backgroundColor = colours;
        this.renderChart(this.data)
    }
}
</script>
