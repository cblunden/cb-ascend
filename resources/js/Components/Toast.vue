<script>
import {collect} from "collect.js";
import _ from "lodash";

export default {
    watch: {
        '$page.props.errors': {
            deep: true,
            immediate: true,
            handler(value) {
                if (! _.isEmpty(value)) {
                    this.toast('error', 'There was an error with your submission. Please check the form and try again.');
                }
            }
        },
        '$page.props.flash': {
            deep: true,
            immediate: true,
            handler(value) {
                if (! _.isEmpty(value)) {
                    this.checkForToasts();
                }
            }
        }
    },
    computed: {
        errors() {
            return collect(this.$page.props.errors || []);
        },
        success() {
            return this.$page.props.flash.success || null;
        },
        error() {
            return this.$page.props.flash.error || null;
        },
        warning() {
            return this.$page.props.flash.warning || null;
        },
        info() {
            return this.$page.props.flash.info || null;
        },
    },
    methods: {
        toast(type, message) {
            this.$toast.open({
                type: type,
                message: message,
                position: 'top-right',
            });
        },
        checkForToasts() {
            if (this.success) {
                this.toast('success', this.success);
            }

            if (this.error) {
                this.toast('error', this.error)
            }

            if (this.warning) {
                this.toast('warning', this.warning);
            }

            if (this.info) {
                this.toast('info', this.info);
            }
        }
    },
}
</script>
