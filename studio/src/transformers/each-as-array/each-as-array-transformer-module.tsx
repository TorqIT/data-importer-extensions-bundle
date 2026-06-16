import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const EachAsArrayTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "eachAsArray",
            label: "Each As Array",
            group: "dataManipulation",
            renderSettings() {
                return null;
            },
        });
    },
};
