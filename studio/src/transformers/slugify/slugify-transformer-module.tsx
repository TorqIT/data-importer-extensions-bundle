import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const SlugifyTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "slugify",
            label: "Slugify",
            group: "dataManipulation",
            renderSettings() {
                return null;
            },
        });
    },
};
