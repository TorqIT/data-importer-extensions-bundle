import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const SlugifyTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "slugify",
            label: "Slugify",
            group: "dataManipulation",
            renderSettings() {
                return null;
            },
        });
    },
};
