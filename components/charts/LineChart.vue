<script>
import { Line } from 'vue-chartjs'

export default {
    name: "LineChart",
    extends: Line,
    props: {
        data: {
            type: Object
        }
    },
    mounted () {
        let colours = ["#C0C0C0", "#FF0000", "#FFFF00", "#00FF00", "#00FFFF", "#0000FF", "#FF00FF", "#eb2f06", "#e58e26", "#78e08f", "#1e3799", "#fad390"]
        for(var i = 0; i < this.data.datasets.length; i++) {
            this.data.datasets[i].borderColor = colours.shift();
            this.data.datasets[i].fill = false;
        }
        this.renderChart(this.data)
    }
}
</script>
